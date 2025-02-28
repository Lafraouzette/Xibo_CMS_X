<?php

namespace Xibo\Helper;

use Phinx\Console\PhinxApplication;
use Phinx\Wrapper\TextWrapper;
use Psr\Container\ContainerInterface;
use Slim\Http\Response as Response;
use Slim\Http\ServerRequest as Request;
use Xibo\Storage\StorageServiceInterface;
use Xibo\Support\Exception\InstallationError;
use Xibo\Support\Sanitizer\SanitizerInterface;

/**
 * Class Install
 * @package Xibo\Helper
 */
class Install
{
    // DB Details
    public $db_create;
    public $db_admin_user;
    public $db_admin_pass;
    public $new_db_host;
    public $new_db_user;
    public $new_db_pass;
    public $new_db_name;
    public $new_ssl_ca;
    public $new_ssl_verify;
    public $existing_db_host;
    public $existing_db_user;
    public $existing_db_pass;
    public $existing_db_name;
    public $existing_ssl_ca;
    public $existing_ssl_verify;

    /** @var ContainerInterface */
    private $container;

    /** @var SanitizerService */
    private $sanitizerService;

    /**
     * Install constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->sanitizerService = $container->get('sanitizerService');
    }

    /**
     * @param $array
     * @return SanitizerInterface
     */
    protected function getSanitizer($array)
    {
        return $this->sanitizerService->getSanitizer($array);
    }

    /**
     * @return array
     */
    public function step1(): array
    {
        return [
            'config' => $this->container->get('configService'),
            'isSettingsPathWriteable' => Environment::checkSettingsFileSystemPermissions()
        ];
    }

    /**
     * @return array
     */
    public function step2(): array
    {
        return [];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws InstallationError
     */
    public function step3(Request $request, Response $response) : Response
    {
        $sanitizedParams = $this->getSanitizer($request->getParams());

        /** @var StorageServiceInterface $store */
        $store = $this->container->get('store');

        // Have we been told to create a new database
        $this->db_create = $sanitizedParams->getInt('db_create');

        // Check all parameters have been specified
        $this->db_admin_user = $sanitizedParams->getString('admin_username');
        $this->db_admin_pass = $sanitizedParams->getString('admin_password');

        $this->new_db_host = $sanitizedParams->getString('host');
        $this->new_db_user = $sanitizedParams->getString('db_username');
        $this->new_db_pass = $sanitizedParams->getString('db_password');
        $this->new_db_name = $sanitizedParams->getString('db_name');
        $this->new_ssl_ca = $sanitizedParams->getString('ssl_ca');
        $this->new_ssl_verify = $sanitizedParams->getCheckbox('ssl_verify') == 1;

        $this->existing_db_host = $sanitizedParams->getString('existing_host');
        $this->existing_db_user = $sanitizedParams->getString('existing_db_username');
        $this->existing_db_pass = $sanitizedParams->getString('existing_db_password');
        $this->existing_db_name = $sanitizedParams->getString('existing_db_name');
        $this->existing_ssl_ca = $sanitizedParams->getString('existing_ssl_ca');
        $this->existing_ssl_verify = $sanitizedParams->getCheckbox('existing_ssl_verify') == 1;

        // If an administrator user name / password has been specified then we should create a new DB
        if ($this->db_create == 1) {
            // Check details for a new database
            if ($this->new_db_host == '') {
                throw new InstallationError(__('Please provide a database host. This is usually localhost.'));
            }

            if ($this->new_db_user == '') {
                throw new InstallationError(__('Please provide a user for the new database.'));
            }

            if ($this->new_db_pass == '') {
                throw new InstallationError(__('Please provide a password for the new database.'));
            }

            if ($this->new_db_name == '') {
                throw new InstallationError(__('Please provide a name for the new database.'));
            }

            if ($this->db_admin_user == '') {
                throw new InstallationError(__('Please provide an admin user name.'));
            }

            // Try to create the new database
            // Try and connect using these details and create the new database
            try {
                $store->connect(
                    $this->new_db_host,
                    $this->db_admin_user,
                    $this->db_admin_pass,
                    null,
                    empty($this->new_ssl_ca) ? null : $this->new_ssl_ca,
                    $this->new_ssl_verify
                );
            } catch (\PDOException $e) {
                throw new InstallationError(sprintf(
                    __('Could not connect to MySQL with the administrator details. Please check and try again. Error Message = [%s]'),
                    $e->getMessage()
                ));
            }

            // Try to create the new database
            try {
                $dbh = $store->getConnection();
                $dbh->exec(sprintf('CREATE DATABASE `%s` CHARACTER SET utf8 COLLATE utf8_general_ci', $this->new_db_name));
            } catch (\PDOException $e) {
                throw new InstallationError(sprintf(__('Could not create a new database with the administrator details [%s]. Please check and try again. Error Message = [%s]'), $this->db_admin_user, $e->getMessage()));
            }

            // Try to create the new user
            $sql = null;
            try {
                $dbh = $store->getConnection();

                // Create the user and grant privileges
                if ($this->new_db_host == 'localhost') {
                    $sql = sprintf(
                        'GRANT ALL PRIVILEGES ON `%s`.* to %s@%s IDENTIFIED BY %s',
                        $this->new_db_name,
                        $dbh->quote($this->new_db_user),
                        $dbh->quote($this->new_db_host),
                        $dbh->quote($this->new_db_pass)
                    );
                } else {
                    $sql = sprintf(
                        'GRANT ALL PRIVILEGES ON `%s`.* to %s@\'%%\' IDENTIFIED BY %s',
                        $this->new_db_name,
                        $dbh->quote($this->new_db_user),
                        $dbh->quote($this->new_db_pass)
                    );
                }
                $dbh->exec($sql);

                // Flush
                $dbh->exec('FLUSH PRIVILEGES');
            } catch (\PDOException $e) {
                throw new InstallationError(sprintf(
                    __('Could not create a new user with the administrator details. Please check and try again. Error Message = [%s]. SQL = [%s].'),
                    $e->getMessage(),
                    $sql
                ));
            }

            // Set our DB details
            $this->existing_db_host = $this->new_db_host;
            $this->existing_db_user = $this->new_db_user;
            $this->existing_db_pass = $this->new_db_pass;
            $this->existing_db_name = $this->new_db_name;
            $this->existing_ssl_ca = $this->new_ssl_ca;
            $this->existing_ssl_verify = $this->new_ssl_verify;

            // Close the connection
            $store->close();
        } else {
            // Check details for a new database
            if ($this->existing_db_host == '') {
                throw new InstallationError(__('Please provide a database host. This is usually localhost.'));
            }

            if ($this->existing_db_user == '') {
                throw new InstallationError(__('Please provide a user for the existing database.'));
            }

            if ($this->existing_db_pass == '') {
                throw new InstallationError(__('Please provide a password for the existing database.'));
            }

            if ($this->existing_db_name == '') {
                throw new InstallationError(__('Please provide a name for the existing database.'));
            }
        }

        // Try and make a connection with this database
        try {
            $store->connect(
                $this->existing_db_host,
                $this->existing_db_user,
                $this->existing_db_pass,
                $this->existing_db_name,
                empty($this->existing_ssl_ca) ? null : $this->existing_ssl_ca,
                $this->existing_ssl_verify
            );
        } catch (\PDOException $e) {
            throw new InstallationError(sprintf(
                __('Could not connect to MySQL with the administrator details. Please check and try again. Error Message = [%s]'),
                $e->getMessage()
            ));
        }

        // Write out a new settings.php
        $fh = fopen(PROJECT_ROOT . '/web/settings.php', 'wt');

        if (!$fh) {
            throw new InstallationError(
                __('Unable to write to settings.php. We already checked this was possible earlier, so something changed.')
            );
        }

        // Get the settings template and issue replacements
        $settings = $this->getSettingsTemplate();

        // Replace instances of $_SERVER vars with our own
        $settings = str_replace('$_SERVER[\'MYSQL_HOST\'] . \':\' . $_SERVER[\'MYSQL_PORT\']', '\'' . $this->existing_db_host . '\'', $settings);
        $settings = str_replace('$_SERVER[\'MYSQL_USER\']', '\'' . $this->existing_db_user . '\'', $settings);
        $settings = str_replace('$_SERVER[\'MYSQL_PASSWORD\']', '\'' . addslashes($this->existing_db_pass) . '\'', $settings);
        $settings = str_replace('$_SERVER[\'MYSQL_DATABASE\']', '\'' . $this->existing_db_name . '\'', $settings);
        $settings = str_replace('$_SERVER[\'MYSQL_ATTR_SSL_CA\']', '\'' . $this->existing_ssl_ca . '\'', $settings);
        $settings = str_replace('$_SERVER[\'MYSQL_ATTR_SSL_VERIFY_SERVER_CERT\']', '\'' . $this->existing_ssl_verify . '\'', $settings);
        $settings = str_replace('define(\'SECRET_KEY\',\'\')', 'define(\'SECRET_KEY\',\'' . Install::generateSecret() . '\');', $settings);

        if (!fwrite($fh, $settings)) {
            throw new InstallationError(__('Unable to write to settings.php. We already checked this was possible earlier, so something changed.'));
        }

        fclose($fh);

        // Run phinx migrate
        $phinx = new TextWrapper(new PhinxApplication(), ['configuration' => PROJECT_ROOT . '/phinx.php']);
        $phinx->getMigrate();

        // If we get here, we want to move on to the next step.
        // This is handled by the calling function (i.e. there is no output from this call, we just reload and move on)
        return $response;
    }

    /**
     * @return array
     */
    public function step4(): array
    {
        return [];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws InstallationError
     */
    public function step5(Request $request, Response $response) : Response
    {
        $sanitizedParams = $this->getSanitizer($request->getParams());
        /** @var StorageServiceInterface $store */
        $store = $this->container->get('store');
        // Configure the user account
        $username = $sanitizedParams->getString('admin_username');
        $password = $sanitizedParams->getString('admin_password');

        if ($username == '') {
            throw new InstallationError(__('Missing the admin username.'));
        }

        if ($password == '') {
            throw new InstallationError(__('Missing the admin password.'));
        }

        // Update user id 1 with these details.
        try {
            $dbh = $store->getConnection();

            $sth = $dbh->prepare('UPDATE `user` SET UserName = :username, UserPassword = :password WHERE UserID = 1 LIMIT 1');
            $sth->execute(array(
                'username' => $username,
                'password' => md5($password)
            ));

            // Update group ID 3 with the user name
            $sth = $dbh->prepare('UPDATE `group` SET `group` = :username WHERE groupId = 3 LIMIT 1');
            $sth->execute(array(
                'username' => $username
            ));

        } catch (\PDOException $e) {
            throw new InstallationError(sprintf(__('Unable to set the user details. This is an unexpected error, please contact support. Error Message = [%s]'), $e->getMessage()));
        }

        return $response;
    }

    /**
     * @return array
     */
    public function step6(): array
    {
        return [
            'serverKey' => Install::generateSecret(6)
        ];
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws InstallationError
     */
    public function step7(Request $request, Response $response) : Response
    {
        $sanitizedParams = $this->getSanitizer($request->getParams());

        /** @var StorageServiceInterface $store */
        $store = $this->container->get('store');

        $server_key = $sanitizedParams->getString('server_key');
        $library_location = $sanitizedParams->getString('library_location');
        $stats = $sanitizedParams->getCheckbox('stats');

        if ($server_key == '') {
            throw new InstallationError(__('Missing the server key.'));
        }

        if ($library_location == '') {
            throw new InstallationError(__('Missing the library location.'));
        }

        // Remove trailing white space from the path given.
        $library_location = trim($library_location);

        if (!is_dir($library_location)) {
            // Make sure they haven't given a file as the library location
            if (is_file($library_location)) {
                throw new InstallationError(__('A file exists with the name you gave for the Library Location. Please choose another location'));
            }

            // Directory does not exist. Attempt to make it
            // Using mkdir recursively, so it will attempt to make any
            // intermediate folders required.
            if (!mkdir($library_location, 0755, true)) {
                throw new InstallationError(__('Could not create the Library Location directory for you. Please ensure the webserver has permission to create a folder in this location, or create the folder manually and grant permission for the webserver to write to the folder.'));
            }
        }

        // Is library_location writable?
        if (!is_writable($library_location)) {
            throw new InstallationError(__('The Library Location you gave is not writable by the webserver. Please fix the permissions and try again.'));
        }

        // Is library_location empty?
        if (count(Install::ls("*", $library_location, true)) > 0) {
            throw new InstallationError(__('The Library Location you gave is not empty. Please give the location of an empty folder'));
        }

        // Check if the user has added a trailing slash. If not, add one.
        if (!((substr($library_location, -1) == '/') || (substr($library_location, -1) == '\\'))) {
            $library_location = $library_location . '/';
        }

        // Attempt to create fonts sub-folder in Library location
        if (!mkdir($library_location . 'fonts', 0777, true)) {
            throw new InstallationError(__('Could not create the fonts sub-folder under Library Location directory for you. Please ensure the webserver has permission to create a folder in this location, or create the folder manually and grant permission for the webserver to write to the folder.'));//phpcs:ignore
        }

        try {
            $dbh = $store->getConnection();

            // Library Location
            $sth = $dbh->prepare('UPDATE `setting` SET `value` = :value WHERE `setting`.`setting` = \'LIBRARY_LOCATION\' LIMIT 1');
            $sth->execute(array('value' => $library_location));

            // Server Key
            $sth = $dbh->prepare('UPDATE `setting` SET `value` = :value WHERE `setting`.`setting` = \'SERVER_KEY\' LIMIT 1');
            $sth->execute(array('value' => $server_key));

            // Default Time zone
            $sth = $dbh->prepare('UPDATE `setting` SET `value` = :value WHERE `setting`.`setting` = \'defaultTimezone\' LIMIT 1');
            $sth->execute(array('value' => date_default_timezone_get()));

            // Phone Home
            $sth = $dbh->prepare('UPDATE `setting` SET `value` = :value WHERE `setting`.`setting` = \'PHONE_HOME\' LIMIT 1');
            $sth->execute([
                'value' => $stats
            ]);
        } catch (\PDOException $e) {
            throw new InstallationError(sprintf(__('An error occurred updating these settings. This is an unexpected error, please contact support. Error Message = [%s]'), $e->getMessage()));
        }

        // Delete install
        if (!@unlink('index.php')) {
            throw new InstallationError(__("Unable to delete install/index.php. Please ensure the web server has permission to unlink this file and retry"));
        }

        return $response;
    }

    /**
     * This function will take a pattern and a folder as the argument and go thru it(recursively if needed)and return the list of
     *               all files in that folder.
     * Link             : http://www.bin-co.com/php/scripts/filesystem/ls/
     * License  : BSD
     * Arguments     :  $pattern - The pattern to look out for [OPTIONAL]
     *                    $folder - The path of the directory of which's directory list you want [OPTIONAL]
     *                    $recursivly - The funtion will traverse the folder tree recursivly if this is true. Defaults to false. [OPTIONAL]
     *                    $options - An array of values 'return_files' or 'return_folders' or both
     * Returns       : A flat list with the path of all the files(no folders) that matches the condition given.
     */
    public static function ls($pattern = '*', $folder = '', $recursivly = false, $options = ['return_files', 'return_folders']): array
    {
        if ($folder) {
            $current_folder = realpath('.');
            if (in_array('quiet', $options)) { // If quiet is on, we will suppress the 'no such folder' error
                if (!file_exists($folder)) return array();
            }

            if (!chdir($folder)) return array();
        }


        $get_files = in_array('return_files', $options);
        $get_folders = in_array('return_folders', $options);
        $both = array();
        $folders = array();

        // Get the all files and folders in the given directory.
        if ($get_files) $both = glob($pattern, GLOB_BRACE + GLOB_MARK);
        if ($recursivly or $get_folders) $folders = glob("*", GLOB_ONLYDIR + GLOB_MARK);

        //If a pattern is specified, make sure even the folders match that pattern.
        $matching_folders = array();
        if ($pattern !== '*') $matching_folders = glob($pattern, GLOB_ONLYDIR + GLOB_MARK);

        //Get just the files by removing the folders from the list of all files.
        $all = array_values(array_diff($both, $folders));

        if ($recursivly or $get_folders) {
            foreach ($folders as $this_folder) {
                if ($get_folders) {
                    //If a pattern is specified, make sure even the folders match that pattern.
                    if ($pattern !== '*') {
                        if (in_array($this_folder, $matching_folders)) array_push($all, $this_folder);
                    } else array_push($all, $this_folder);
                }

                if ($recursivly) {
                    // Continue calling this function for all the folders
                    $deep_items = Install::ls($pattern, $this_folder, $recursivly, $options); # :RECURSION:
                    foreach ($deep_items as $item) {
                        array_push($all, $this_folder . $item);
                    }
                }
            }
        }

        if ($folder) chdir($current_folder);
        return $all;
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateSecret($length = 12): string
    {
        # Generates a random 12 character alphanumeric string to use as a salt
        mt_srand((double)microtime() * 1000000);
        $key = "";
        for ($i = 0; $i < $length; $i++) {
            $c = mt_rand(0, 2);
            if ($c == 0) {
                $key .= chr(mt_rand(65, 90));
            } elseif ($c == 1) {
                $key .= chr(mt_rand(97, 122));
            } else {
                $key .= chr(mt_rand(48, 57));
            }
        }

        return $key;
    }

    private function getSettingsTemplate()
    {
        return <<<END
<?php

/*
 * Xibo - Digital Signage - http://www.xibo.org.uk
 *
 * This file is part of Xibo - and is automatically generated by the installer
 *
 * You should not need to edit this file, unless your SQL connection details have changed.
 */

defined('XIBO') or die(__("Sorry, you are not allowed to directly access this page.") . "<br />" . __("Please press the back button in your browser."));

global \$dbhost;
global \$dbuser;
global \$dbpass;
global \$dbname;
global \$dbssl;
global \$dbsslverify;

\$dbhost = \$_SERVER['MYSQL_HOST'] . ':' . \$_SERVER['MYSQL_PORT'];
\$dbuser = \$_SERVER['MYSQL_USER'];
\$dbpass = \$_SERVER['MYSQL_PASSWORD'];
\$dbname = \$_SERVER['MYSQL_DATABASE'];
\$dbssl = \$_SERVER['MYSQL_ATTR_SSL_CA'];
\$dbsslverify = \$_SERVER['MYSQL_ATTR_SSL_VERIFY_SERVER_CERT'];

if (!defined('SECRET_KEY'))
    define('SECRET_KEY','');

if (file_exists('/var/www/cms/custom/settings-custom.php'))
    include_once('/var/www/cms/custom/settings-custom.php');

END;
    }
}
