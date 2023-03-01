<?php

namespace plugin\email\app\admin\model;


use plugin\admin\app\model\Option;
use plugin\email\api\Email;

/**
 * 邮件模版相关
 */
class Template
{
    /**
     * 获取模版
     * @param $name
     * @return mixed|null
     */
    public static function get($name)
    {
        $value = Option::where('name', static::templateNameToOptionName($name))->value('value');
        return $value ? json_decode($value, true) : null;
    }

    /**
     * 保存模版
     * @param $name
     * @param $value
     * @return void
     */
    public static function save($name, $value)
    {
        $optionName = static::templateNameToOptionName($name);
        if (!$option = Option::where('name', $optionName)->first()) {
            $option = new Option;
        }
        $option->name = $optionName;
        $option->value = json_encode($value, JSON_UNESCAPED_UNICODE);
        $option->save();
    }

    /**
     * 删除模版
     * @param array $names
     * @return void
     */
    public static function delete(array $names)
    {
        foreach ($names as $index => $name) {
            $names[$index] = static::templateNameToOptionName($name);
        }
        Option::whereIn('name', $names)->delete();
    }

    /**
     * 模版名到option名转换
     * @param string $name
     * @return string
     */
    public static function templateNameToOptionName(string $name): string
    {
        return Email::TEMPLATE_OPTION_PREFIX . $name;
    }

    /**
     * option名到模版名转换
     * @param string $name
     * @return string
     */
    public static function optionNameToTemplateName(string $name): string
    {
        return substr($name, strlen(Email::TEMPLATE_OPTION_PREFIX));
    }

}
