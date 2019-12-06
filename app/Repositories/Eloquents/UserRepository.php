<?php
namespace App\Repositories\Eloquents;

use Auth;
use App\Models\User;
use App\Repositories\Eloquents\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        return User::class;
    }
    public function store($request)
    {
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('public/images');
            $data['avatar'] = strstr($path, '/');
        }
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = bcrypt($request->password);
        $data['role'] = $request->role;
        $user = User::create($data);
        return $user;
    }
}
