<?php

namespace app\user\controller;

use think\Controller;
use think\Request;
use app\user\model\User;
use think\facade\Session;

class Auth extends Controller
{
    // protected $middleware 则是初始化一个中间件,而 except 方法表示,当前控制器下有哪些方法是不使用中间件的.
    protected $middleware = [
        'UserAuthorize' => [
            'except' => ['create', 'save']
        ]
    ];

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch();
        // if (Session::has('user')) {
        //     $user = Session::get('user');
        //     return redirect('user/auth/read')->params(['id' => $user->id]);
        // } else {
        //     $token = $this->request->token('__token__', 'sha1');
        //     $this->assign('token', $token);
        //     return $this->fetch();

        // }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $requestData = $request->post();
        // validate($requestData, 'app\user\validate\Auth') 的前部分表示传入的值,后半部分表示要使用的验证器.
        $result = $this->validate($requestData, 'app\user\validate\Auth');
        if (true !== $result) {
            // redirect('user/auth/create') 是跳转到对应的 控制器/方法
            // with('validate',$result) 则是 redirect 提供的一个快捷 flash 闪存 的方法,与 Session::flash('validate',$result); 效果一样.
            return redirect('user/auth/create')->with('validate', $result);
        } else {
            $user = User::create($requestData);
            Session::set('user', $user);
            return redirect('user/auth/read')->params(['id' => $user->id]);
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $user = User::find($id);
        $this->assign([
            'user' => $user
        ]);
        return $this->fetch();
        // if (Session::has('user')) {
        //     $user = User::find($id); // User::find($id) 是模型的一个查询语法,默认查询 $id(主键值)
        //     $token = $this->request->token('__token__', 'sha1');
        //     $this->assign([
        //         'user' => $user,
        //         'token' => $token
        //     ]);
        //     return $this->fetch();
        // } else {
        //     return redirect('user/session/create')->with('validate', '请先登录');
        // }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {

        $user = User::find($id);
        $this->assign(['user' => $user]);
        return $this->fetch();
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {

        $requestData = $request->put();
        $result = $this->validate($requestData, 'app\user\validate\UpdateUser');

        if (true !== $result) {
            return redirect('user/auth/edit', ['id' => $id])->with('validate', $result);
        } else {
            $name = $requestData['name'];
            User::where('id', $id)->update(['name' => $name]);
            Session::set('user.name', $name);
            return redirect('user/auth/edit', ['id' => $id])->with('validate', '修改成功');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}