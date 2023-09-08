<?php


class Ini
{
    private string $file;
    public array $all = [];

    public function __construct($file)
    {
        $this->file = $file;
        if (is_file($file)) {
            $rs = parse_ini_file($file, true);
            if (!empty($rs)) $this->all = $rs;
        }
    }

    public static function load($file): Ini
    {
        return new static($file);
    }

    public function get($key, $default = null)
    {
        if (!is_file($this->file)) return $default;
        $arr = explode('.', $key);
        if (count($arr) === 1) return $this->all[$key] ?? $default;
        if (count($arr) === 2) return $this->all[$arr[0]][$arr[1]] ?? $default;
        if (count($arr) > 2) {
            return $this->all[substr($key, 0, strrpos($key, '.'))][substr($key, strrpos($key, '.') + 1)] ?? $default;
        }
        return $default;
    }

    public function set($key, $value): bool
    {
        $arr = explode('.', $key);
        if (count($arr) === 1) {
            $this->all = array_merge($this->all, [$key => $value]);
        } else if (count($arr) === 2) {
            $this->all = array_merge_recursive($this->all, [$arr[0] => [$arr[1] => $value]]);
        } else {
            return false;
        }

        return !(file_put_contents($this->file, $this->arr2ini($this->all)) === false);
    }

    public function setAll($data): bool
    {
        $data = array_merge($this->all, $data);
        return !(file_put_contents($this->file, $this->arr2ini($data)) === false);
    }

    private function arrayToIni($data): string
    {
        if (empty($data)) return '';

        $str = '';
        foreach ($data as $k => $v) {
            if (!is_array($v)) {
                $str .= "$k=$v" . PHP_EOL;
            } else {
                $str .= "[$k]" . PHP_EOL;
                foreach ($v as $kk => $vv) {
                    if (!is_string($vv)) continue;
                    $str .= "$kk=$vv" . PHP_EOL;
                }
            }
        }
        return $str;
    }

    /**
     * @param array $a
     * @param array $parent
     * @return string
     * @see https://stackoverflow.com/questions/17316873/convert-array-to-an-ini-file/17317168#17317168
     */
    private function arr2ini(array $a, array $parent = []): string
    {
        $out = '';
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                $sec = array_merge($parent, (array)$k);
                $out .= '[' . join('.', $sec) . ']' . PHP_EOL;
                $out .= $this->arr2ini($v, $sec);
            } else {
                $out .= "$k=\"$v\"" . PHP_EOL;
            }
        }
        return $out;
    }

}