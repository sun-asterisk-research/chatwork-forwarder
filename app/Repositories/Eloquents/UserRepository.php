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

    public function getAllAndSearch($perPage, $searchParams)
    {
        $query = $this->model->orderBy('created_at', 'desc');

        if ($searchParams) {
            $searchParams = $this->handleSearchParams(['name', 'email'], $searchParams);

            return $query->search($searchParams, $perPage);
        } else {
            return $query->paginate($perPage);
        }
    }

    public function update($id, $request)
    {
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('public/images');
            $data['avatar'] = strstr($path, '/');
        }
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        if ($request->password != '') {
            $data['password'] = bcrypt($request->password);
        }
        $data['role'] = $request->role;
        $result = User::find($id);
        if ($result) {
            $result->update($data);

            return $result;
        }

        return false;
    }

    public function findByEmail($email)
    {
        $result = $this->model->where('email', $email)->first();
        
        return $result;
    }
}
