<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

class TransactionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM categories');    
    }

    /**
     * test raw query Transaction Success
     */
    public function testTransactionSuccess(): void {
        DB::transaction(function(){
            DB::insert('INSERT INTO categories(id, name, created_at, description) 
                VALUES (:id, :name, :created_at, :description)',[
                    'id'=>'GADGET', 
                    'name'=>'Gadget', 
                    'created_at'=>'2024-10-10 10:10:10',
                    'description'=>'Gadget Category'
                ]);

            DB::insert('INSERT INTO categories(id, name, created_at, description) 
                VALUES (:id, :name, :created_at, :description)',[
                    'id'=>'FOOD', 
                    'name'=>'Food', 
                    'created_at'=>'2024-10-10 10:10:10',
                    'description'=>'Food Category'
                ]);
        });

        $result = DB::select('SELECT id, name, created_at, description FROM categories');
        assertCount(2, $result);
    }

    /**
     * test raw query Transaction Failed
     */
    public function testTransactionFailed(): void {
        try {
            DB::transaction(function(){
                DB::insert('INSERT INTO categories(id, name, created_at, description) 
                    VALUES (:id, :name, :created_at, :description)',[
                        'id'=>'GADGET', 
                        'name'=>'Gadget', 
                        'created_at'=>'2024-10-10 10:10:10',
                        'description'=>'Gadget Category'
                    ]);

                DB::insert('INSERT INTO categories(id, name, created_at, description) 
                    VALUES (:id, :name, :created_at, :description)',[
                        'id'=>'GADGET', 
                        'name'=>'Gadget', 
                        'created_at'=>'2024-10-10 10:10:10',
                        'description'=>'Gadget Category'
                    ]);
            });

        } catch(QueryException $exception) {
            // expected
        }
    
        $result = DB::select('SELECT id, name, created_at, description FROM categories');
        assertCount(0, $result);
    }

    /**
     * test raw query Transaction Manual Failed
     */
    public function testTransactionManualFailed(): void {
        try {
            DB::beginTransaction();
            DB::insert('INSERT INTO categories(id, name, created_at, description) 
                VALUES (:id, :name, :created_at, :description)',[
                    'id'=>'GADGET', 
                    'name'=>'Gadget', 
                    'created_at'=>'2024-10-10 10:10:10',
                    'description'=>'Gadget Category'
                ]);

            DB::insert('INSERT INTO categories(id, name, created_at, description) 
                VALUES (:id, :name, :created_at, :description)',[
                    'id'=>'GADGET', 
                    'name'=>'Gadget', 
                    'created_at'=>'2024-10-10 10:10:10',
                    'description'=>'Gadget Category'
                ]);
            DB::commit();

        } catch(QueryException $exception) {
            DB::rollBack();
        }
    
        $result = DB::select('SELECT id, name, created_at, description FROM categories');
        assertCount(0, $result);
    }

    /**
     * test raw query Transaction Manual Success
     */
    public function testTransactionManualSuccess(): void {
        try {
            DB::beginTransaction();
            DB::insert('INSERT INTO categories(id, name, created_at, description) 
                VALUES (:id, :name, :created_at, :description)',[
                    'id'=>'GADGET', 
                    'name'=>'Gadget', 
                    'created_at'=>'2024-10-10 10:10:10',
                    'description'=>'Gadget Category'
                ]);

            DB::insert('INSERT INTO categories(id, name, created_at, description) 
                VALUES (:id, :name, :created_at, :description)',[
                    'id'=>'FOOD', 
                    'name'=>'Food', 
                    'created_at'=>'2024-10-10 10:10:10',
                    'description'=>'Food Category'
                ]);
            DB::commit();

        } catch(QueryException $exception) {
            DB::rollBack();
        }
    
        $result = DB::select('SELECT id, name, created_at, description FROM categories');
        assertCount(2, $result);
    }
}
