<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property varchar $name name @property timestamp $created_at created at @property timestamp $updated_at updated at

 */
class Category extends Model

{

    /**
     * Database table name
     */
    protected $table = 'categories';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name','code'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function createRecord($code, $description, $status = 1){
        $asset = ChartOfAccount::where('class', 'A16')->first();
        $cost = ChartOfAccount::where('class', 'C50')->first();
        $revenue = ChartOfAccount::where('class', 'R40')->first();
        if(($a = GeneralAccount::createRecord($asset->class,$code.' - '.$description)) &&
            ($c = GeneralAccount::createRecord($cost->class,$code.' - '.$description)) &&
            ($r = GeneralAccount::createRecord($revenue->class,$code.' - '.$description))){
            $record = new self;
            $record->code = $code;
            $record->name = $description;
            $record->asset_account = $a->id;
            $record->cost_account = $c->id;
            $record->revenue_account = $r->id;
            if($record->save())
                return $record;
        }

        return false;
    }

    public function has_record(){
        $products = Product::where('category_id', $this->id)->first();
        if(!$products)
            return false;
        return true;
    }

    public function deleteAccounts(){
        if(GeneralAccount::whereIn('id', [$this->asset_account, $this->cost_account, $this->revenue_account])->delete())
            return true;
        return false;
    }

}
