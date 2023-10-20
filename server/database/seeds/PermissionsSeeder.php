<?php
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Spatie\Permission\Models\Permission;
class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = ['Deal List', 'Create Deal', 'Show Deal', 'Delete Deal', 'Update Deal',
        'Calendar User List', 'Create Calendar User', 'Show Calendar User', 'Delete Calendar User', 'Update Calendar User',
        'Comment List', 'Create Comment', 'Show Comment', 'Delete Comment', 'Update Comment',
        'Document List', 'Create Document', 'Show Document', 'Delete Document', 'Update Document', 'Upload Document',
        'Lead List', 'Create Lead', 'Show Lead', 'Delete Lead', 'Update Lead', 'Update Lead Comment', 'Mass Delete Lead', 'Mass Comment Lead', 'Add Mass Comment', 'Lead Comment List', 'Create Lead Comment', 'Update Lead Comment', 'Delete Lead Comment',
        'Option List', 'Create Option', 'Show Option', 'Delete Option', 'Update Option',
        'Service List', 'Create Service', 'Show Service', 'Delete Service', 'Update Service',
        'Ticket List', 'Create Ticket', 'Show Ticket', 'Delete Ticket', 'Update Ticket'];
        $permissionsFaker = Faker::create(Permission::class);
        for ($i = 0; $i < count($permissions); $i++) {
            $data = [
                'name' => $permissions[$i],
                'guard_name' => 'api',
            ];
            $permission = new Permission($data);
            $permission->save();
        }
    }
}