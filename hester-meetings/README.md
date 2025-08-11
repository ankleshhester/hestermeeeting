# Employee Details Migration

This migration creates the `employee_details` table in the database, which will store information about employees. The table includes the following fields:

- `employee_code`: A unique code for each employee.
- `name`: The full name of the employee.
- `email`: The email address of the employee.
- `mobile`: The mobile phone number of the employee.
- `extension`: The phone extension for the employee.
- `monthly_cost`: The monthly cost associated with the employee.
- `hourly_cost`: The hourly cost associated with the employee.

The migration includes methods to create and drop the table.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('employee_details', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile');
            $table->string('extension')->nullable();
            $table->decimal('monthly_cost', 10, 2);
            $table->decimal('hourly_cost', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_details');
    }
}
```