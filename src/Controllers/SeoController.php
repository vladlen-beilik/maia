<?php

namespace SpaceCode\Maia\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use SpaceCode\Maia\Models\Seo;
use SpaceCode\Maia\Tools\SeoTool;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\ResolvesFields;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Laravel\Nova\Fields\FieldCollection;
use Illuminate\Support\Facades\Validator;

class SeoController extends Controller
{
    use ResolvesFields,
        ConditionallyLoadsAttributes;


    public function get(Request $request)
    {
        $fields = $this->assignToPanels(trans('maia::resources.seo'), $this->availableFields());
        $panels = $this->panelsWithDefaultLabel(trans('maia::resources.seo'), new NovaRequest);

        $addResolveCallback = function (&$field) {
            if (!empty($field->attribute)) {
                $seo = Seo::where('key', $field->attribute)->first();
                $field->resolve([$field->attribute => isset($seo) ? $seo->value : '']);
            }
        };

        $fields->each(function (&$field) use ($addResolveCallback) {
            $addResolveCallback($field);
        });

        return response()->json([
            'panels' => $panels,
            'fields' => $fields,
        ], 200);
    }

    public function save(NovaRequest $request)
    {
        $fields = $this->availableFields();

        $rules = [];
        foreach ($fields as $field) {
            $fakeResource = new \stdClass;
            $fakeResource->{$field->attribute} = seo($field->attribute);
            $field->resolve($fakeResource, $field->attribute); // For nova-translatable support
            $rules = array_merge($rules, $field->getUpdateRules($request));
        }

        Validator::make($request->all(), $rules)->validate();

        $fields->whereInstanceOf(Resolvable::class)->each(function ($field) use ($request) {
            if (empty($field->attribute)) return;

            // For nova-translatable support
//            if (!empty($field->meta['translatable']['original_attribute'])) $field->attribute = $field->meta['translatable']['original_attribute'];

            $existingRow = Seo::where('key', $field->attribute)->first();

            $tempResource =  new \stdClass;
            $field->fill($request, $tempResource);

            if (!property_exists($tempResource, $field->attribute)) return;

            if (isset($existingRow)) {
                $existingRow->update(['value' => $tempResource->{$field->attribute}]);
            } else {
                Seo::create([
                    'key' => $field->attribute,
                    'value' => $tempResource->{$field->attribute},
                ]);
            }
        });

        return response('', 204);
    }

    protected function availableFields()
    {
        return new FieldCollection(($this->filter(SeoTool::getFields())));
    }

    protected function fields(Request $request)
    {
        return SeoTool::getFields();
    }
}
