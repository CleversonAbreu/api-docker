<?php 

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function findAll(int $qtd) : LengthAwarePaginator
    {
        return $this->user->paginate($qtd);
    }

    public function findById(int $id) : User
    {
        return $this->user->find($id);
    }

    public function insert(array $user)  : User
    {
        return $this->user->create($user);
    }

    public function update(int $id, array $user) : int
    {
        return $this->user->whereId($id)->update($user);
    }

    public function delete(int $id) : bool
    {
        return $this->user->whereId($id)->delete();
    }

    public function emailExists(string $email) : bool
    {
        return $this->user->where('email', $email)->exists();
    }

    public function changePassword(array $user) : User
    {
        $userModel = $this->user->where('email', $user['email'])->first();

        $userModel->update([
            'password' => Hash::make($user['newPassword'])
        ]);
    
        return $userModel; 
    }
}