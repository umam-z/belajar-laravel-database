<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

class RawQueryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM categories');    
    }

    public function Crud(): void {
        DB::insert('INSERT INTO categories(id, name, created_at, description) VALUES (?,?,?,?)',[
            'GADGET', 'Gadget', '2024-10-10 10:1010','Gadget Category'
        ]);

        $result = DB::select('SELECT id, name, created_at, description FROM categories WHERE id =?', ['GADGET']);

        assertCount(1, $result);
        assertEquals('GADGET', $result[0]->id);
        assertEquals('Gadget', $result[0]->name);
        assertEquals('Gadget Category', $result[0]->description);
        assertEquals('2024-10-10 10:1010', $result[0]->created_at);
    }
}
