<?php

namespace SpaceCode\Maia\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;

class ContactForm extends Resource
{
    protected $casts = [
        'index' => 'array'
    ];

    /**
     * @var string
     */
    public static $model = \SpaceCode\Maia\Models\ContactForm::class;

    /**
     * @var string
     */
    public static $title = 'title';

    /**
     * @var array
     */
    public static $search = [
        'sender', 'title',
    ];

    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return trans('maia::navigation.sidebar-communication');
    }

    /**
     * @return array|string|null
     */
    public static function label()
    {
        return trans('maia::resources.contactForms');
    }

    /**
     * @return array|string|null
     */
    public static function singularLabel()
    {
        return trans('maia::resources.contactForm');
    }

    /**
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [

            // ID
            ID::make()->asBigInt()->sortable(),

            // Sender
            Text::make(trans('maia::resources.sender'), 'sender')->sortable()->readonly(),

            // Title
            Text::make(trans('maia::resources.title'), 'title')->readonly(),

            // Contacts
            Textarea::make(trans('maia::resources.contacts'), 'contacts')->displayUsing(function () {
                if(is_null($this->contacts)) {
                    return null;
                } else {
                    $array = '';
                    foreach (json_decode($this->contacts) as $key => $value) {
                        $val = $key === 'budget' ? "$" . $value : $value;
                        $array .= ucfirst($key) . ": " . $val . "\n";
                    }
                    return $array;
                }
            })->hideFromIndex()->readonly(),

            // Description
            Textarea::make(trans('maia::resources.description'), 'description')->displayUsing(function () {
                if(is_null($this->description)) {
                    return null;
                } else {
                    $array = '';
                    foreach (json_decode($this->description) as $key => $value) {
                        $val = $key === 'budget' ? "$" . $value : $value;
                        $array .= ucfirst($key) . ": " . $val . "\n";
                    }
                    return $array;
                }
            })->hideFromIndex()->readonly(),
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
