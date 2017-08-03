<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BhuAdmin extends Model
{
    protected $connection = 'mysql2';

    protected $table = 'users';

    public $timestamps = false;

    public function LoginAdminUser($data)
    {
        $username = $data['username'];
        $password = md5($data['password']);
        $user = $this->where('user_id', $username)
            ->where('pwd', $password)
            ->whereIn('role',['Lecturer','HOD','Dean','Senate','Transcript'])
            ->first();

        if($user) {
            session($user->toArray());
            return $user;
        }

        return false;
    }

    public function changeAdminPassword($password)
    {
        $password = md5($password['password']);
        $userId = session('user_id');
        return $this->where('user_id', $userId)->update(['pwd' => $password]);
    }


}
