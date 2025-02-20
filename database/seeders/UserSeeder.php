<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;
use App\Rrhh\OrganizationalUnit;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ou = OrganizationalUnit::first();

        $user = new User();
        $user->id = 12345678;
        $user->dv = 9;
        $user->name = "Administrador";
        $user->fathers_family = "Paterno";
        $user->mothers_family = "Materno";
        $user->password = bcrypt('admin');
        $user->position = "Administrator";
        $user->email = "alvaro.torres@redsalud.gob.cl";
        $user->organizationalUnit()->associate($ou);
        $user->save();
        $user->assignRole('god', 'dev','RRHH: admin');
        $user->givePermissionTo(Permission::all());

        $user = new User();
        $user->id = 15287582;
        $user->dv = 7;
        $user->name = "Alvaro";
        $user->fathers_family = "Torres";
        $user->mothers_family = "Fuchslocher";
        $user->email = "sistemas.ssi@redsalud.gob.cl";
        $user->password = bcrypt('admin');
        $user->position = "Profesional SIDRA";
        $user->organizationalUnit()->associate($ou);
        $user->save();
        $user->assignRole('god', 'dev');
        $user->givePermissionTo(Permission::all());

        $user = User::Create(['id'=>10278387, 'dv'=>5, 'name'=>'José', 'fathers_family'=>'Donoso', 'mothers_family' => 'Carrera',
            'email'=>'jose.donosoc@redsalud.gob.cl','password'=>bcrypt('10278387'), 'position'=>'Jefe', 'organizational_unit_id'=>$ou->id]);
        $user->assignRole('dev');
        $user->givePermissionTo(Permission::all());

        $user = User::Create(['id'=>14107361, 'dv'=>3, 'name'=>'Pamela', 'fathers_family'=>'Villagrán', 'mothers_family' => 'Alvarez',
            'email'=>'pamela.villagran@redsalud.gob.cl','password'=>bcrypt('14107361'), 'position'=>'Administrativa', 'organizational_unit_id'=>$ou->id]);
        $user->assignRole('dev');
        $user->givePermissionTo(Permission::all());

        $user = User::Create(['id'=>16966444, 'dv'=>7, 'name'=>'Jorge', 'fathers_family'=>'Miranda', 'mothers_family' => 'Lopez',
            'email'=>'jorge.mirandal@redsalud.gob.cl','password'=>bcrypt('16966444'), 'position'=>'Profesional SIDRA', 'organizational_unit_id'=>$ou->id]);
        $user->assignRole('god','dev');
        $user->givePermissionTo(Permission::all());

        $user = User::Create(['id'=>15924400, 'dv'=>8, 'name'=>'Cristian', 'fathers_family'=>'Carpio', 'mothers_family' => 'Diaz',
            'email'=>'cristian.carpio@redsalud.gob.cl','password'=>bcrypt('15924400'), 'position'=>'Profesional SIDRA', 'organizational_unit_id'=>$ou->id]);
        $user->assignRole('dev');
        $user->givePermissionTo(Permission::all());

        $user = User::Create(['id'=>16351236, 'dv'=>'k', 'name'=>'German', 'fathers_family'=>'Zuñiga', 'mothers_family' => 'Codocedo',
            'email'=>'german.zuniga@redsalud.gob.cl','password'=>bcrypt('admin'), 'position'=>'Profesional SIDRA', 'organizational_unit_id'=>$ou->id]);
        $user->assignRole('god','dev');
        $user->givePermissionTo(Permission::all());

        $user = User::Create(['id'=>16350137, 'dv'=>6, 'name'=>'Alvaro', 'fathers_family'=>'Lupa', 'mothers_family' => 'Huanca',
            'email'=>'alvaro.lupa@redsalud.gob.cl','password'=>bcrypt('admin'), 'position'=>'Profesional SIDRA', 'organizational_unit_id'=>$ou->id]);
        $user->assignRole('god','dev');
        $user->givePermissionTo(Permission::all());
    }
}
