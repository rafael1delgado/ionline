<?php

namespace App\Rrhh;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Requirements\Category;
use App\Models\Profile\Subrogation;
use App\Models\Establishment;

class OrganizationalUnit extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'organizational_units';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'level',
        'organizational_unit_id',
        'establishment_id',
        'sirh_function',
        'sirh_ou_id',
        'sirh_cost_center'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    

    public function users()
    {
        return $this->hasMany(User::class)->orderBy('name');
    }

    public function father()
    {
        return $this->belongsTo('\App\Rrhh\OrganizationalUnit', 'organizational_unit_id');
    }

    public function childs()
    {
        return $this->hasMany('\App\Rrhh\OrganizationalUnit', 'organizational_unit_id');
    }

    public function authorities()
    {
        return $this->hasMany('\App\Rrhh\Authority');
    }

    public function documents()
    {
        return $this->hasMany('\App\Models\Documents\Document');
    }

    public function documentEvents()
    {
        return $this->hasMany('\App\Models\Documents\DocumentEvent');
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function requestForms()
    {
      return $this->hasMany(RequestForm::class, 'applicant_ou_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function currentManager()
    {
        return $this->hasOne(Authority::class)
            ->with('user')
            ->where('date',today())
            ->where('type','manager');
    }

    public function currentDelegate()
    {
        return $this->hasOne(Authority::class)
            ->with('user')
            ->where('date',today())
            ->where('type','delegate');
    }

    public function currentSecretary()
    {
        return $this->hasOne(Authority::class)
            ->with('user')
            ->where('date',today())
            ->where('type','secretary');
    }

    public function subrogationsManager()
    {
        return $this->hasMany(Subrogation::class)
            ->where('type','manager');
    }

    public function scopeSearch($query, $name)
    {
        if($name != "")
        {
            return $query->where('name', 'LIKE', '%'.$name.'%');
        }
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', preg_replace('/\s+/', ' ', $this->name));
        $initials = '';
        foreach ($words as $word) {
            if ($word != 'de' && $word != 'y' && $word != 'la' && $word != 'e' && $word != 'las' && $word != 'del'
                && $word != 'al' && $word != 'en' && $word != 'el') {
                if ($word === 'Subdirección') {
                    $initials .= 'SD';
                } elseif ($word === 'S.A.M.U.' || $word === 'P.E.S.P.I.' || $word === 'P.R.A.I.S.' || $word === 'O.I.R.S.' ||
                    $word === 'GES/PPV') {
                    $initials .= $word;
                } else {
                    $initials .= $word[0];
                }
            }
        }
        return $initials;
    }

    public function getTree($getBrothers = false, $getChilds = false)
    {
        // ARBOL PARA GOOGLE CHART
        $tree = collect([]);
        $root = $this;

        for($i = 1; $i <= $this->level; $i++)
        {
            if($this->id == $root->id)
            {
                $info['v'] = $root->name;
                $info['f'] = "{$root->name}<div style='color:red; font-style:italic'>Funcionario</div>";
            }
            else
            {
                $info = $root->name;
            }

            $sheet = [$info, $root->father->name ?? '', ''];
            $tree->push($sheet);

            $root = $root->father;
        }

        if($this->father && $getBrothers)
        {
            foreach($this->father->childs as $child)
            {
                $sheet = [$child->name, $child->father->name ?? '', ''];
                $tree->push($sheet);
            }
        }

        if($getChilds)
        {
            foreach($this->childs as $child)
            {
                $sheet = [$child->name, $child->father->name ?? '', ''];
                $tree->push($sheet);
            }
        }

        return $tree;
    }

    public function getTreeDocPdf()
    {
        $root = $this;
        $tree_doc_pdf = array();

        for($i = 1; $i <= $this->level; $i++)
        {
            $info_doc_pdf['level'] = $root->level;
            $info_doc_pdf['name'] = $root->name;
            $tree_doc_pdf[] = $info_doc_pdf;
            $root = $root->father;
        }

        $print = array_multisort(array_column($tree_doc_pdf, "level"), SORT_ASC, $tree_doc_pdf);

        $col = array_column( $tree_doc_pdf, "level" );
        array_multisort( $col, SORT_ASC, $tree_doc_pdf );

        return $tree_doc_pdf;
    }

    public function getTreeAttribute()
    {
        return $this->getTree();
    }

    public function getTreeWithBrothersAttribute()
    {
        return $this->getTree(true);
    }

    public function getTreeWithChildsAttribute()
    {
        return $this->getTree(false, true);
    }

    public static function getOrganizationalUnitsBySearch($searchText)
    {
        $organizationalUnits = OrganizationalUnit::query();
        $array_search = explode(' ', $searchText);
        foreach($array_search as $word){
            $organizationalUnits->where(function($q) use($word){
                $q->where('name', 'LIKE', '%'.$word.'%')
                ->where('establishment_id', Auth::user()->organizationalUnit->establishment_id);
            });
        }

        return $organizationalUnits;
    }

    public function getOrganizationalUnitByLevel($level)
    {
        if($this->level == $level) return $this;
        return $this->father->getOrganizationalUnitByLevel($level);
    }
}
