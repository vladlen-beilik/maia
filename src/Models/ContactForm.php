<?php
namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use SpaceCode\Maia\Contracts\ContactForm as ContactFormContract;
use SpaceCode\Maia\Exceptions\ContactFormDoesNotExist;

class ContactForm extends Model implements ContactFormContract
{
    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * ContactForm constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable('contact_forms');
    }

    /**
     * @param array $attributes
     * @return Builder|Model
     */
    public static function create(array $attributes = [])
    {
        return static::query()->create($attributes);
    }

    /**
     * @param string $title
     * @return ContactFormContract
     * @throws ContactFormDoesNotExist
     */
    public static function findByTitle(string $title): ContactFormContract
    {
        $contactForm = static::where('title', $title)->first();
        if (! $contactForm) {
            throw ContactFormDoesNotExist::named($title);
        }
        return $contactForm;
    }

    /**
     * @param int $id
     * @return ContactFormContract
     * @throws ContactFormDoesNotExist
     */
    public static function findById(int $id): ContactFormContract
    {
        $contactForm = static::where('id', $id)->first();
        if (! $contactForm) {
            throw ContactFormDoesNotExist::withId($id);
        }
        return $contactForm;
    }
}
