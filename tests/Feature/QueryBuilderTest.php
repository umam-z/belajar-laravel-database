<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Database\Seeders\CounterSeeder;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('DELETE FROM products');    
        DB::delete('DELETE FROM categories');    
        DB::delete('DELETE FROM counters');    
    }

    /**
     * test query builder Insert
     */
    public function testInsert(): void {
        DB::table('categories')->insert([
            'id'=>'GADGET', 
            'name'=>'Gadget', 
            'created_at'=>'2024-10-10 10:10:10',
            'description'=>'Gadget Category'
        ]);
        DB::table('categories')->insert([
            'id'=>'FOOD', 
            'name'=>'Food', 
            'created_at'=>'2024-10-10 10:10:10',
            'description'=>'Food Category'
        ]);

        $result = DB::select('SELECT count(id) AS total FROM categories');
        assertEquals(2, $result[0]->total);
    }

    /**
     * test query builder Select
     */
    public function testSelect(): void {
        DB::table('categories')->insert([
            'id'=>'GADGET', 
            'name'=>'Gadget', 
            'created_at'=>'2024-10-10 10:10:10',
            'description'=>'Gadget Category'
        ]);
        DB::table('categories')->insert([
            'id'=>'FOOD', 
            'name'=>'Food', 
            'created_at'=>'2024-10-10 10:10:10',
            'description'=>'Food Category'
        ]);

        $collection = DB::table('categories')->select(['name', 'id'])->get();
        assertNotNull($collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });

        // $result = DB::select('SELECT count(id) AS total FROM categories');
        // assertEquals(2, $result[0]->total);
    }

    public function insertCategories(): void {
        $this->seed(CategorySeeder::class);
    }

    /**
     * test query builder Where
     */
    public function testWhere(): void {
        $this->insertCategories();
        $collection = DB::table('categories')->where(function (Builder $builder) {
            $builder->where('id', '=', 'SMARTPHONE');
            $builder->orWhere('id', '=', 'FASHION');
            // SELECT * FROM categories WHERE(id = 'SMARTPHONE' OR id = 'FASHION')
        })->get();

        assertCount(2, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    /**
     * test query builder Between Method
     */
    public function testBetween(): void {
        $this->insertCategories();
        $collection = DB::table('categories')->whereBetween('created_at', ['2024-9-10 10:10:10', '2024-11-10 10:10:10'])->get();

        assertCount(4, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    /**
     * test query builder Where In
     */
    public function testWhereIn(): void {
        $this->insertCategories();
        $collection = DB::table('categories')->whereIn('id', ['SMARTPHONE', 'FOOD'])->get();

        assertCount(2, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    /**
     * test query builder Where Null 
    */
    public function testWhereNull(): void {
        $this->insertCategories();
        $collection = DB::table('categories')->whereNull(['description'])->get();

        assertCount(2, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    /**
     * test query builder Where Date 
    */
    public function testWhereDate(): void {
        $this->insertCategories();
        $collection = DB::table('categories')->whereDate('created_at', '2024-10-10')->get();

        assertCount(4, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    /**
     * test query builder Update
    */
    public function testUpdate(): void {
        $this->insertCategories();
        DB::table('categories')->where('id', '=', 'SMARTPHONE')->update(['name'=>'Handphone']);

        $collection = DB::table('categories')->where('name', '=', 'Handphone')->get();

        assertCount(1, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    /**
     * test query builder Update anda insert
    */
    public function testUpsert(): void {
        $this->insertCategories();
        
        DB::table('categories')->updateOrInsert([
            'id'=>'VOUCHER'
        ], [
            'name'=>'Voucher',
            'description'=>'Ticket and Voucher',
            'created_at'=>'2024-10-10 10:10:10',
        ]);

        $collection = DB::table('categories')->where('id', '=', 'VOUCHER')->get();

        assertCount(1, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    /**
     * test query builder Increament
    */
    public function testIncrement(): void {
        $this->seed(CounterSeeder::class);
        
        DB::table('counters')->where('id', '=', 'sample')->increment('counter', 2);

        $collection = DB::table('counters')->where('id', '=', 'sample')->get();

        assertCount(1, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    /**
     * test query builder Delete
    */
    public function testDelete(): void {
        $this->insertCategories();
        
        DB::table('categories')->where('id', '=', 'SMARTPHONE')->delete();

        $collection = DB::table('categories')->where('id', '=', 'SMARTPHONE')->get();

        assertCount(0, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    public function insertProducts(): void {
        $this->insertCategories();
        
        DB::table('products')->insert([
            'id'=>'1', 
            'name'=>'iPhone 14 Pro Max', 
            'category_id'=>'SMARTPHONE',
            'price'=>20000000
        ]);
        DB::table('products')->insert([
            'id'=>'2', 
            'name'=>'Samsung Galaxy S21 Ultra', 
            'category_id'=>'SMARTPHONE',
            'price'=>18000000
        ]);
    }

    /**
     * test query builder Join
    */
    public function testJoin(): void {
        $this->insertProducts();

        $collection = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'products.name', 'products.price', 'categories.name as category_name')
            ->get();


        assertCount(2, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    /**
     * test query builder Ordering
    */
    public function testOrdering(): void {
        $this->insertProducts();

        $collection = DB::table('products')
            ->whereNotNull('id')
            ->orderBy('price', 'desc')
            ->orderBy('name', 'asc')
            ->get();


        assertCount(2, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    /**
     * test query builder Paging
    */
    public function testPaging(): void {
        $this->insertProducts();

        $collection = DB::table('products')
            ->skip(0)
            ->take(2)
            ->get();


        assertCount(2, $collection);

        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
    }

    public function insertManyCategories(): void {
        
        for ($i=0; $i < 100; $i++) { 
            DB::table('categories')->insert([
                'id'=>"CATEGORY-$i", 
                'name'=>"Category-$i", 
                'created_at'=>'2024-10-10 10:10:10'
            ]);
        }
    }

    /**
     * test query builder Chunk
    */
    public function testChunk(): void {
        $this->insertManyCategories();

        DB::table('categories')->orderBy('id')
        ->chunk(10, function ($categories) {
            assertNotNull($categories);

            Log::info('start chunk'); 
            $categories->each(function ($category) {
                Log::info(json_encode($category));
            });
            Log::info('end chunk');
        });
        assertTrue(true);
    }

    /**
     * test query builder Lazy
    */
    public function testLazy(): void {
        $this->insertManyCategories();

        $collection = DB::table('categories')->orderBy('id')->lazy(10)->take(5);
        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
        assertTrue(true);
    }

    /**
     * test query builder Cursor
    */
    public function testCursor(): void {
        $this->insertManyCategories();

        $collection = DB::table('categories')->orderBy('id')->cursor();
        $collection->each(function ($items) {
            Log::info(json_encode($items));
        });
        assertTrue(true);
    }

    /**
     * test query builder Agregat
    */
    public function testAgregat(): void {
        $this->insertProducts();

        $result = DB::table('products')->count('id');
        assertEquals(2, $result);

        $result = DB::table('products')->min('price');
        assertEquals(18_000_000, $result);

        $result = DB::table('products')->max('price');
        assertEquals(20_000_000, $result);

        $result = DB::table('products')->avg('price');
        assertEquals(19_000_000, $result);

        $result = DB::table('products')->sum('price');
        assertEquals(38_000_000, $result);
    }

    /**
     * test query builder Query Builder Raw
    */
    public function testQueryBuilderRaw(): void {
        $this->insertProducts();

        $collection = DB::table('products')
            ->select(
                DB::raw('count(id) as total_products'),
                DB::raw('min(price) as min_price'),
                DB::raw('max(price) as max_price')
            )
            ->get();

        assertEquals(2, $collection[0]->total_products);
        assertEquals(18_000_000, $collection[0]->min_price);
        assertEquals(20_000_000, $collection[0]->max_price);
    }

    public function insertProductsFood(): void {
        DB::table('products')->insert([
            'id'=>'3', 
            'name'=>'Bakso', 
            'category_id'=>'FOOD',
            'price'=>15000
        ]);
        DB::table('products')->insert([
            'id'=>'4', 
            'name'=>'Mie Ayam', 
            'category_id'=>'FOOD',
            'price'=>10000
        ]);
    }

    /**
     * test query builder Group By
     */
    public function testGroupBy(): void {
        $this->insertProducts();
        $this->insertProductsFood();

        $collection = DB::table('products')
            ->select('category_id', DB::raw('count(*) as total_products'))
            ->groupBy('category_id')
            ->orderBy('category_id', 'desc')
            ->get();

        assertCount(2, $collection);
        assertEquals('SMARTPHONE', $collection[0]->category_id);
        assertEquals('FOOD', $collection[1]->category_id);
        assertEquals(2, $collection[0]->total_products);
        assertEquals(2, $collection[0]->total_products);
    }

    /**
     * test query builder Group By Having
     */
    public function testGroupByHaving(): void {
        $this->insertProducts();
        $this->insertProductsFood();

        $collection = DB::table('products')
            ->select('category_id', DB::raw('count(*) as total_products'))
            ->groupBy('category_id')
            ->having(DB::raw('count(*)'), '>', 2)
            ->orderBy('category_id', 'desc')
            ->get();

        assertCount(0, $collection);
    }

    /**
     * test query builder Locking
     */
    public function testLocking(): void {
        $this->insertProducts();
        DB::transaction(function () {
            $collection = DB::table('products')
            ->where('id', '=', '1')
            ->lockForUpdate()
            ->get();
            assertCount(1, $collection);
        });

    }

    /**
     * test query builder Pagination
     */
    public function testPagination(): void {
        $this->insertCategories();
        $paginate = DB::table('categories')->paginate(perPage: 2, page: 2);
        assertEquals(2, $paginate->currentPage());
        assertEquals(2, $paginate->perPage());
        assertEquals(2, $paginate->lastPage());
        assertEquals(4, $paginate->total());

        $collection = $paginate->items();
        assertCount(2, $collection);

        foreach ($collection as $item) {
            Log::info(json_encode($item));
        }
    }

    /**
     * test query builder Iterate All Pagination
     */
    public function testIterateAllPagination(): void {
        $this->insertCategories();
        $page = 1;

        while (true) {
            $paginate = DB::table('categories')->paginate(perPage: 2, page: $page);
    
            if ($paginate->isEmpty()) {
                break;
            } else {
                $page++;
                $collection = $paginate->items();
                assertCount(2, $collection);
                
                foreach ($collection as $item) {
                    Log::info(json_encode($item));
                }
            }
        }
    }

    /**
     * test query builder Cursor Pagination
     */

    public function testCursorPagination(): void {
        $this->insertCategories();

        $cursor = 'id';

        while (true) {
            $paginate = DB::table('categories')->orderBy('id')->cursorPaginate(perPage: 2, cursor: $cursor);

            foreach ($paginate->items() as $item) {
               assertNotNull($item);
               Log::info(json_encode($item));
            }

            $cursor = $paginate->nextCursor();
            if($cursor == null) {
                break;
            }
        }
    }
}
