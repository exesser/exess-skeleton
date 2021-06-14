<?php
namespace ExEss\Cms\Cleaner;

class RequestCleaner
{
    private const XSS_CLEANUP = [
        '&quot;' => '&#38;',
        '"' => '&quot;',
        "'" => '&#039;',
        '<' => '&lt;',
        '>' => '&gt;'
    ];

    public function __invoke(): void
    {
        $this->cleanSpecialArguments();
        $this->cleanIncomingData();
    }

    private function cleanSpecialArguments(): void
    {
        if (isset($_SERVER['PHP_SELF'])) {
            if (!empty($_SERVER['PHP_SELF'])) {
                $this->cleanString($_SERVER['PHP_SELF'], 'SAFED_GET');
            }
        }
        if (!empty($_POST) && !empty($_POST['parent_type'])) {
            $this->cleanString($_POST['parent_type']);
        }
        if (!empty($_GET) && !empty($_GET['from'])) {
            $this->cleanString($_GET['from']);
        }
        if (!empty($_GET) && !empty($_GET['case_number'])) {
            $this->cleanString($_GET['case_number'], 'AUTO_INCREMENT');
        }
        $this->cleanSuperglobals('offset');
    }

    private function cleanIncomingData(): int
    {
        $req = \array_map([$this, 'secureXss'], $_REQUEST);
        $post = \array_map([$this, 'secureXss'], $_POST);
        $get = \array_map([$this, 'secureXss'], $_GET);

        // PHP cannot stomp out superglobals reliably
        foreach ($post as $k => $v) {
            $_POST[$k] = $v;
        }
        foreach ($get as $k => $v) {
            $_GET[$k] = $v;
        }
        foreach ($req as $k => $v) {
            $_REQUEST[$k] = $v;
            $this->secureXssKey($k);
        }
        // Any additional variables that need to be cleaned should be added here
        if (isset($_REQUEST['action'])) {
            $this->cleanString($_REQUEST['action']);
        }
        if (isset($_REQUEST['module'])) {
            $this->cleanString($_REQUEST['module']);
        }
        if (isset($_REQUEST['record'])) {
            $this->cleanString($_REQUEST['record'], 'STANDARDSPACE');
        }
        if (isset($_REQUEST['language'])) {
            $this->cleanString($_REQUEST['language']);
        }
        if (isset($_REQUEST['offset'])) {
            $this->cleanString($_REQUEST['offset']);
        }

        if (isset($_REQUEST['lvso'])) {
            $this->setSuperglobals('lvso', (\strtolower($_REQUEST['lvso']) === 'desc') ? 'desc' : 'asc');
        }
        // Clean "offset" and "order_by" parameters in URL
        foreach ($_REQUEST as $key => $val) {
            if ($this->stringEnd($key, '_offset')) {
                $this->cleanString($_REQUEST[$key], 'ALPHANUM'); // keep this ALPHANUM for disable_count_query
                $this->setSuperglobals($key, $_REQUEST[$key]);
            } elseif ($this->stringEnd($key, '_ORDER_BY')) {
                $this->cleanString($_REQUEST[$key], 'SQL_COLUMN_LIST');
                $this->setSuperglobals($key, $_REQUEST[$key]);
            }
        }

        return 0;
    }

    /**
     * Returns TRUE if $str ends with $end
     */
    private function stringEnd(string $str, string $end): bool
    {
        return \substr($str, \strlen($str) - \strlen($end)) == $end;
    }

    /**
     * cleans the given key in superglobals $_GET, $_POST, $_REQUEST.
     */
    private function cleanSuperglobals(string $key): void
    {
        if (isset($_GET[$key])) {
            $this->cleanString($_GET[$key], 'ALPHANUM');
        }
        if (isset($_POST[$key])) {
            $this->cleanString($_POST[$key], 'ALPHANUM');
        }
        if (isset($_REQUEST[$key])) {
            $this->cleanString($_REQUEST[$key], 'ALPHANUM');
        }
    }

    /**
     * @param $key
     * @param mixed $val
     */
    private function setSuperglobals(string $key, $val): void
    {
        $_GET[$key] = $val;
        $_POST[$key] = $val;
        $_REQUEST[$key] = $val;
    }

    /**
     * Designed to take a string passed in the URL as a parameter and clean all "bad" data from it.
     *
     * @param string $filter which corresponds to a regular expression to use; choices are:
     *                             "STANDARD" ( default )
     *                             "STANDARDSPACE"
     *                             "FILE"
     *                             "NUMBER"
     *                             "SQL_COLUMN_LIST"
     *                             "PATH_NO_URL"
     *                             "SAFED_GET"
     *                             "AUTO_INCREMENT"
     *                             "ALPHANUM"
     */
    private function cleanString(string $str, string $filter = 'STANDARD'): string
    {
        $filters = [
            'STANDARD' => '#[^A-Z0-9\-_\.\@]#i',
            'STANDARDSPACE' => '#[^A-Z0-9\-_\.\@\ ]#i',
            'FILE' => '#[^A-Z0-9\-_\.]#i',
            'NUMBER' => '#[^0-9\-]#i',
            'SQL_COLUMN_LIST' => '#[^A-Z0-9\(\),_\.]#i',
            'PATH_NO_URL' => '#://#i',
            'SAFED_GET' => '#[^A-Z0-9\@\=\&\?\.\/\-_~+|\:\\\\]#i', /* range of allowed characters in a GET string */
            'AUTO_INCREMENT' => '#[^0-9\-,\ ]#i',
            'ALPHANUM' => '#[^A-Z0-9\-]#i',
        ];

        if (\preg_match($filters[$filter], $str)) {
            $cleanstr = \strip_tags($this->secureXss($str));
            die("Bad data passed in {$cleanstr}");
        }

        return $str;
    }

    /**
     * @param mixed $value
     * @return string|array
     */
    private function secureXss($value)
    {
        if (\is_array($value)) {
            $new = [];
            foreach ($value as $key => $val) {
                $new[$key] = $this->secureXss($val);
            }

            return $new;
        }

        $value = \preg_replace(['/javascript:/i', '/\0/'], ['java script:', ''], $value);
        $value = \preg_replace('/javascript:/i', 'java script:', $value);

        return \str_replace(\array_keys(self::XSS_CLEANUP), \array_values(self::XSS_CLEANUP), $value);
    }

    private function secureXssKey(string $value): void
    {
        $matches = [];
        \preg_match('/[\'"<>]/', $value, $matches);
        if (!empty($matches)) {
            die("Bad data passed in " . $value);
        }
    }
}
