<?php

namespace luguohuakai\ini;

class Ini
{
    /**
     * @var string $file 文件绝对路径
     */
    private string $file;
    /**
     * @var array $all 所有数据
     */
    public array $all = [];

    /**
     * Ini constructor.
     * @param string $file 文件绝对路径
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        if (is_file($file)) {
            $rs = parse_ini_file($file, true);
            if (!empty($rs)) $this->all = $rs;
        }
    }

    /**
     * 加载ini文件
     * @param string $file 文件绝对路径
     * @return Ini
     */
    public static function load(string $file): Ini
    {
        return new static($file);
    }

    /**
     * 获取值
     * @param string $key 支持点操作,如: test.name 最多支持2级
     * @param $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
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

    /**
     * 设置值
     * @param string $key 支持点操作,如: test.name 最多支持2级
     * @param $value
     * @return bool
     */
    public function set(string $key, $value): bool
    {
        $keys = explode('.', $key);
        if (count($keys) === 1) {
            $this->all = array_merge($this->all, [$key => $value]);
        } else if (count($keys) === 2) {
            $this->all[$keys[0]][$keys[1]] = $value;
        } else {
            return false;
        }

        return !(file_put_contents($this->file, $this->arr2ini($this->all)) === false);
    }

    /**
     * 一次更新或设置多个键值对
     * @param $data
     * ```
     * $ini->setAll([
     * 'test.test2' => 'test2',
     * 'test.test3' => 'test3',
     * 'srun.name' => 'srun',
     * 'age' => '19',
     * ]);
     * ```
     * @return bool
     */
    public function setAll($data): bool
    {
        foreach ($data as $k => $v) {
            $this->set($k, $v);
        }
        return true;
        // 直接用点的方式连接两级的key 可能会跟section有冲突
//        $data = array_merge($this->all, $data);
//        return !(file_put_contents($this->file, $this->arr2ini($data)) === false);
    }

    /**
     * @param $data
     * @return string
     * @deprecated
     */
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
        $out_no_section = '';
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                $sec = array_merge($parent, (array)$k);
                $out .= '[' . join('.', $sec) . ']' . PHP_EOL;
                $out .= $this->arr2ini($v, $sec);
            } else {
                $out_no_section .= "$k=\"$v\"" . PHP_EOL;
            }
        }
        return $out_no_section . $out;
    }

}