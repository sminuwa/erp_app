<?php
namespace App\Transformers;

use LaraCrud\Helpers\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Models\Category;



class CategoryTransformer extends TransformerAbstract
{
     /**
     * @var array
     */
    private $validParams = ['q', 'limit', 'page','fields'];

    /**
     * @var array
     */
    protected $availableIncludes = [];

     /**
      * @var array
      */
    protected $defaultIncludes = [];


    public function transform(Category $category)
    {
        $data= [
			"id" => $category->id,
			"name" => $category->name,
			"slug" => $category->slug,
			"created_at" => $category->created_at,
			"updated_at" => $category->updated_at,

        ];
        return $this->filterFields($data);

    }

    
}