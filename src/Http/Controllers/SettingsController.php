<?php

namespace SpaceCode\Maia\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use SpaceCode\Maia\Models\Settings;
use SpaceCode\Maia\SettingsTool;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\ResolvesFields;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;
use Laravel\Nova\Fields\FieldCollection;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    use ResolvesFields,
        ConditionallyLoadsAttributes;


    public function get(Request $request)
    {
        $fields = $this->assignToPanels(__('Settings'), $this->availableFields());
        $panels = $this->panelsWithDefaultLabel(__('Settings'), new NovaRequest);

        $addResolveCallback = function (&$field) {
            if (!empty($field->attribute)) {
                $setting = Settings::where('key', $field->attribute)->first();
                $field->resolve([$field->attribute => isset($setting) ? $setting->value : '']);
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
            $fakeResource->{$field->attribute} = setting($field->attribute);
            $field->resolve($fakeResource, $field->attribute); // For nova-translatable support
            $rules = array_merge($rules, $field->getUpdateRules($request));
        }

        Validator::make($request->all(), $rules)->validate();

        $fields->whereInstanceOf(Resolvable::class)->each(function ($field) use ($request) {
            if (empty($field->attribute)) return;

            // For nova-translatable support
            if (!empty($field->meta['translatable']['original_attribute'])) $field->attribute = $field->meta['translatable']['original_attribute'];

            $existingRow = Settings::where('key', $field->attribute)->first();

            $tempResource =  new \stdClass;
            $field->fill($request, $tempResource);

            if (!property_exists($tempResource, $field->attribute)) return;

            if (isset($existingRow)) {
                if($field->attribute === 'site_favicon' && $existingRow->value !== $tempResource->{$field->attribute}) {
                    $existing_array = explode('.', $existingRow->value);
                    $existing_mime = $existing_array[sizeof($existing_array) - 1];
                    $existing_path = str_replace('.' . $existing_mime,'', $existingRow->value);
                    $temp_array = explode('.', $tempResource->{$field->attribute});
                    $temp_mime = $temp_array[sizeof($temp_array) - 1];
                    $temp_path = str_replace('.' . $temp_mime,'', $tempResource->{$field->attribute});
                    foreach (['32', '128', '152', '167', '180', '192', '196'] as $size) {
                        Storage::disk(config('maia.filemanager.disk', 'public'))->delete($existing_path . '_' . $size . '.' . $existing_mime);
                        Storage::disk(config('maia.filemanager.disk', 'public'))->copy($tempResource->{$field->attribute}, $temp_path . '_' . $size . '.' . $temp_mime);
                    }
                }
                if($field->attribute === 'site_timezone') {
                    changeEnv('APP_TIMEZONE', $tempResource->{$field->attribute});
                }
                $existingRow->update(['value' => $tempResource->{$field->attribute}]);
            } else {
                if($field->attribute === 'site_favicon') {
                    $temp_array = explode('.', $tempResource->{$field->attribute});
                    $temp_mime = $temp_array[sizeof($temp_array) - 1];
                    $temp_path = str_replace('.' . $temp_mime,'', $tempResource->{$field->attribute});
                    foreach (['32', '128', '152', '167', '180', '192', '196'] as $size) {
                        Storage::disk(config('maia.filemanager.disk', 'public'))->copy($tempResource->{$field->attribute}, $temp_path . '_' . $size . '.' . $temp_mime);
                    }
                }
                if($field->attribute === 'site_timezone') {
                    changeEnv('APP_TIMEZONE', $tempResource->{$field->attribute});
                }
                Settings::create([
                    'key' => $field->attribute,
                    'value' => $tempResource->{$field->attribute},
                ]);
            }
        });

        return response('', 204);
    }

    public function deleteImage(Request $request, $fieldName)
    {
        $existingRow = Settings::where('key', $fieldName)->first();
        if (isset($existingRow)) $existingRow->update(['value' => null]);
        return response('', 204);
    }

    protected function availableFields()
    {
        return new FieldCollection(($this->filter(SettingsTool::getFields())));
    }

    protected function fields(Request $request)
    {
        return SettingsTool::getFields();
    }
}
