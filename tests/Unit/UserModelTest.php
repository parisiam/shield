<?php

namespace Tests\Unit;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class UserModelTest extends TestCase
{
    use DatabaseTestTrait;

    protected $namespace;
    protected $refresh = true;

    private function createUserModel(): UserModel
    {
        return new UserModel();
    }

    public function testSaveInsertUser(): void
    {
        $users = $this->createUserModel();

        $user = $this->createNewUser();

        $users->save($user);

        $user = $users->findByCredentials(['email' => 'foo@bar.com']);
        $this->seeInDatabase('auth_identities', [
            'user_id' => $user->id,
            'secret'  => 'foo@bar.com',
        ]);
        $this->seeInDatabase('users', [
            'id'     => $user->id,
            'active' => 0,
        ]);
    }

    public function testInsertUserObject(): void
    {
        $users = $this->createUserModel();

        $user = $this->createNewUser();

        $users->insert($user);

        $user = $users->findByCredentials(['email' => 'foo@bar.com']);
        $this->seeInDatabase('auth_identities', [
            'user_id' => $user->id,
            'secret'  => 'foo@bar.com',
        ]);
        $this->seeInDatabase('users', [
            'id'     => $user->id,
            'active' => 0,
        ]);
    }

    public function testInsertUserArray(): void
    {
        $users = $this->createUserModel();

        $user = $this->createNewUser();

        $userArray = $user->toArray();
        $id        = $users->insert($userArray);

        $this->dontSeeInDatabase('auth_identities', [
            'user_id' => $id,
            'secret'  => 'foo@bar.com',
        ]);
        $this->seeInDatabase('users', [
            'id'     => $id,
            'active' => 0,
        ]);
    }

    private function createNewUser(): User
    {
        $user           = new User();
        $user->username = 'foo';
        $user->email    = 'foo@bar.com';
        $user->password = 'password';
        $user->active   = 0;

        return $user;
    }

    public function testSaveUpdateUserObjectWithUserDataToUpdate(): void
    {
        $users = $this->createUserModel();
        $user  = $this->createNewUser();
        $users->save($user);

        $user = $users->findByCredentials(['email' => 'foo@bar.com']);

        $user->username = 'bar';
        $user->email    = 'bar@bar.com';
        $user->active   = 1;

        $users->save($user);

        $this->seeInDatabase('auth_identities', [
            'user_id' => $user->id,
            'secret'  => 'bar@bar.com',
        ]);
        $this->seeInDatabase('users', [
            'id'     => $user->id,
            'active' => 1,
        ]);
    }

    public function testUpdateUserObjectWithUserDataToUpdate(): void
    {
        $users = $this->createUserModel();
        $user  = $this->createNewUser();
        $users->save($user);

        $user = $users->findByCredentials(['email' => 'foo@bar.com']);

        $user->username = 'bar';
        $user->email    = 'bar@bar.com';
        $user->active   = 1;

        $users->update(null, $user);

        $this->seeInDatabase('auth_identities', [
            'user_id' => $user->id,
            'secret'  => 'bar@bar.com',
        ]);
        $this->seeInDatabase('users', [
            'id'     => $user->id,
            'active' => 1,
        ]);
    }

    public function testUpdateUserArrayWithUserDataToUpdate(): void
    {
        $users = $this->createUserModel();
        $user  = $this->createNewUser();
        $users->save($user);

        $user = $users->findByCredentials(['email' => 'foo@bar.com']);

        $user->username = 'bar';
        $user->email    = 'bar@bar.com';
        $user->active   = 1;

        $userArray = $user->toArray();
        $users->update(null, $userArray);

        $this->dontSeeInDatabase('auth_identities', [
            'user_id' => $user->id,
            'secret'  => 'bar@bar.com',
        ]);
        $this->seeInDatabase('users', [
            'id'     => $user->id,
            'active' => 1,
        ]);
    }

    public function testSaveUpdateUserObjectWithoutUserDataToUpdate(): void
    {
        $users = $this->createUserModel();
        $user  = $this->createNewUser();
        $users->save($user);

        $user = $users->findByCredentials(['email' => 'foo@bar.com']);

        $user->email = 'bar@bar.com';

        $users->save($user);

        $this->seeInDatabase('auth_identities', [
            'user_id' => $user->id,
            'secret'  => 'bar@bar.com',
        ]);
    }

    public function testUpdateUserObjectWithoutUserDataToUpdate(): void
    {
        $users = $this->createUserModel();
        $user  = $this->createNewUser();
        $users->save($user);

        $user = $users->findByCredentials(['email' => 'foo@bar.com']);

        $user->email = 'bar@bar.com';

        $users->update(null, $user);

        $this->seeInDatabase('auth_identities', [
            'user_id' => $user->id,
            'secret'  => 'bar@bar.com',
        ]);
    }
}
