<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin {name} {phone_number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $validationResult = $this->validate();

            if ($validationResult !== 0) {
                return $validationResult;
            }
            DB::beginTransaction();

            $user = User::create([
                'name' => $this->argument('name'),
                'phone_number' => $this->argument('phone_number'),
                'password' => Hash::make('secret'),
                'type' => User::TYPE_ADMIN,
            ]);

            Admin::create([
                'user_id' => $user->id,
            ]);

            DB::commit();
            $this->info('Admin registration succeeded.');

            return 0;
        } catch (Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());

            return 1;
        }
    }

    private function validate(): int
    {
        $validator = Validator::make([
            'name' => $this->argument('name'),
            'phone_number' => $this->argument('phone_number'),
        ], [
            'name' => ['required'],
            'phone_number' => ['required', 'regex:/^\+2519[0-9]{8}$/', 'max:13' , 'unique:users,phone_number'],
        ]);

        if ($validator->fails()) {
            $this->info('Admin registration failed. See error(s) below.');

            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return 1;
        }

        return 0;
    }
}
