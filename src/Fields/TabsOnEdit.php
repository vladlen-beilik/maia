<?php
namespace SpaceCode\Maia\Fields;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Panel;
use Laravel\Nova\Http\Requests\NovaRequest;

trait TabsOnEdit
{
    /**
     * Resolve the creation fields.
     *
     * @param NovaRequest $request
     * @return FieldCollection
     */
    public function creationFields(NovaRequest $request)
    {
        return new FieldCollection(
            [
                'Tabs' => [
                    'component' => 'tabs',
                    'fields'    => $this->removeNonCreationFields($request, $this->resolveFields($request)),
                    'panel'     => Panel::defaultNameForCreate($request->newResource()),
                ],
            ]
        );
    }

    /**
     * Fill a new model instance using the given request.
     *
     * @param NovaRequest $request
     * @param  Model     $model
     * @return array
     */
    public static function fill(NovaRequest $request, $model)
    {
        return static::fillFields(
            $request, $model,
            (new static($model))->parentCreationFields($request)
        );
    }

    /**
     * @param NovaRequest $request
     * @param $model
     * @return
     */
    public static function fillForUpdate(NovaRequest $request, $model)
    {
        return static::fillFields(
            $request, $model,
            (new static($model))->parentUpdateFields($request)
        );
    }

    /**
     * @param NovaRequest $request
     * @return FieldCollection
     */
    public function parentCreationFields(NovaRequest $request)
    {
        return parent::creationFields($request);
    }

    /**
     * @param NovaRequest $request
     * @return FieldCollection
     */
    public function parentUpdateFields(NovaRequest $request)
    {
        return parent::updateFields($request);
    }

    /**
     * @param  NovaRequest $request
     * @return mixed
     */
    public static function rulesForCreation(NovaRequest $request)
    {
        return static::formatRules($request, (new static(static::newModel()))
            ->parentCreationFields($request)
            ->mapWithKeys(function ($field) use ($request) {
                return $field->getCreationRules($request);
            })->all());
    }

    /**
     * Get the validation rules for a resource update request.
     *
     * @param NovaRequest $request
     * @param null $resource
     * @return array
     */
    public static function rulesForUpdate(NovaRequest $request, $resource = null)
    {
        return static::formatRules($request, (new static(static::newModel()))
            ->parentUpdateFields($request)
            ->mapWithKeys(function ($field) use ($request) {
                return $field->getUpdateRules($request);
            })->all());
    }

    /**
     * Resolve the update fields.
     *
     * @param NovaRequest $request
     * @return FieldCollection
     */
    public function updateFields(NovaRequest $request)
    {
        return new FieldCollection(
            [
                'Tabs' => [
                    'component' => 'tabs',
                    'fields'    => $this->removeNonUpdateFields($request, $this->resolveFields($request)),
                    'panel'     => Panel::defaultNameForUpdate($request->newResource()),
                ],
            ]
        );
    }

    /**
     * Assign the fields with the given panels to their parent panel.
     *
     * @param  string  $label
     * @param FieldCollection $fields
     * @return FieldCollection
     */
    protected function assignToPanels($label, FieldCollection $fields)
    {
        return $fields->map(function ($field) use ($label) {
            if ( !is_array($field) && !$field->panel ) {
                $field->panel = $label;
            }

            return $field;
        });
    }
}
