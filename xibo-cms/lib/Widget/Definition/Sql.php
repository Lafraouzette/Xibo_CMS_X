<?php


namespace Xibo\Widget\Definition;

/**
 * SQL definitions
 */
class Sql
{
    const DISALLOWED_KEYWORDS = [
        ';',
        'INSERT',
        'UPDATE',
        'SELECT',
        'FROM',
        'WHERE',
        'DELETE',
        'TRUNCATE',
        'TABLE',
        'ALTER',
        'GRANT',
        'REVOKE',
        'CREATE',
        'DROP',
    ];
}
