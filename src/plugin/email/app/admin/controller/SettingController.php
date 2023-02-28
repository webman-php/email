<?php

namespace plugin\email\app\admin\controller;

use PHPMailer\PHPMailer\Exception;
use plugin\admin\app\model\Option;
use plugin\email\api\Email;
use plugin\email\api\Install;
use support\Request;
use support\Response;
use function view;

/**
 * 邮件设置
 */
class SettingController
{

    /**
     * 邮件设置页
     * @return Response
     */
    public function index()
    {
        return view('setting/index');
    }

    /**
     * 获取设置
     * @return Response
     */
    public function get(): Response
    {
        $name = Email::OPTION_NAME;
        $setting = Option::where('name', $name)->value('value');
        $setting = $setting ? json_decode($setting, true) : [
            'Host' => 'smtp.qq.com',
            'Username' => '',
            'Password' => '',
            'SMTPSecure' => 'ssl',
            'Port' => 465,
            'From' => '',
        ];
        return json(['code' => 0, 'msg' => 'ok', 'data' => $setting]);
    }

    /**
     * 更改设置
     * @param Request $request
     * @return Response
     */
    public function save(Request $request): Response
    {
        $data = [
            'Host' => $request->post('Host'),
            'Username' => $request->post('Username'),
            'Password' => $request->post('Password'),
            'SMTPSecure' => $request->post('SMTPSecure'),
            'Port' => $request->post('Port'),
            'From' => $request->post('From'),
        ];
        $value = json_encode($data);
        $name = Email::OPTION_NAME;
        $option = Option::where('name', $name)->first();
        if ($option) {
            Option::where('name', $name)->update(['value' => $value]);
        } else {
            $option = new Option();
            $option->name = $name;
            $option->value = $value;
            $option->save();
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

    /**
     * 邮件测试
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function test(Request $request): Response
    {
        $from = $request->post('From');
        $to = $request->post('To');
        $subject = $request->post('Subject');
        $content = $request->post('Content');
        Email::send($from, $to, $subject, $content);
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
