<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('districts', function (Blueprint $table) {
            if (Schema::hasColumn('districts', 'name') == true) {
                try {
                    Schema::table('districts', function (Blueprint $table) {
                        $table->dropIndex('districts_name_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('name',150)->unique()->change();
            }
        });
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'name') == true) {
                try {
                    Schema::table('cities', function (Blueprint $table) {
                        $table->dropIndex('cities_name_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('name',150)->unique()->change();
            }
        });
        Schema::table('states', function (Blueprint $table) {
            if (Schema::hasColumn('states', 'name') == true) {
                try {
                    Schema::table('states', function (Blueprint $table) {
                        $table->dropIndex('states_name_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('name',150)->unique()->change();
            }
        });
        Schema::table('countries', function (Blueprint $table) {
            if (Schema::hasColumn('countries', 'name') == true) {
                try {
                    Schema::table('countries', function (Blueprint $table) {
                        $table->dropIndex('countries_name_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('name',150)->unique()->change();
            }
        });
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'name') == true) {
                try {
                    Schema::table('branches', function (Blueprint $table) {
                        $table->dropIndex('branches_name_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('name',150)->unique()->change();
            }
        });
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'company_name') == true) {
                try {
                    Schema::table('companies', function (Blueprint $table) {
                        $table->dropIndex('companies_company_name_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('company_name',150)->unique()->change();
            }
        });
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'gst_number') == true) {
                $table->string('gst_number',16)->nullable()->change();
            }
        });
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'office_address') == true) {
                $table->string('office_address',500)->nullable()->change();
            }
        });
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'pan_number') == true) {
                $table->string('pan_number',10)->nullable()->change();
            }
        });
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'remarks') == true) {
                $table->string('remarks',1000)->nullable()->change();
            }
        });
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'website') == true) {
                $table->string('website',200)->nullable()->change();
            }
        });
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'cin_number') == true) {
                $table->string('cin_number',25)->nullable()->change();
            }
        });
        Schema::table('banks', function (Blueprint $table) {
            if (Schema::hasColumn('banks', 'name') == true) {
                try {
                    Schema::table('banks', function (Blueprint $table) {
                        $table->dropIndex('banks_name_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('name',200)->unique()->change();
            }
        });
        Schema::table('ifscs', function (Blueprint $table) {
            if (Schema::hasColumn('ifscs', 'ifsc_code') == true) {
                try {
                    Schema::table('ifscs', function (Blueprint $table) {
                        $table->dropIndex('ifscs_ifsc_code_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('ifsc_code',11)->unique()->change();
            }
        });
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'name') == true) {
                try {
                    Schema::table('roles', function (Blueprint $table) {
                        $table->dropIndex('roles_name_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('name',100)->unique()->change();
            }
        });
        Schema::table('pay_terms', function (Blueprint $table) {
            if (Schema::hasColumn('pay_terms', 'name') == true) {
                try {
                    Schema::table('pay_terms', function (Blueprint $table) {
                        $table->dropIndex('pay_terms_name_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('name',100)->unique()->change();
            }
        });
        Schema::table('accounts', function (Blueprint $table) {
            if (Schema::hasColumn('accounts', 'name') == true) {
                try {
                    Schema::table('accounts', function (Blueprint $table) {
                        $table->dropIndex('accounts_name_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('name')->unique()->change();
            }
        });

        Schema::table('years', function (Blueprint $table) {
            if (Schema::hasColumn('years', 'year') == true) {
                try {
                    Schema::table('years', function (Blueprint $table) {
                        $table->dropIndex('years_year_unique');
                    });    //code...
                } catch (\Throwable $th) {
                    logger($th->getMessage());
                }
                $table->string('year', 8)->unique()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
