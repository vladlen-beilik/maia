<?php

namespace SpaceCode\Maia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use SpaceCode\Maia\Fields\Editor;

class PendingAttachment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nova_pending_ckeditor_attachments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Persist the given draft's pending attachments.
     *
     * @param  string $draftId
     * @param Editor $field
     * @param  mixed $model
     * @return void
     */
    public static function persistDraft($draftId, Editor $field, $model)
    {
        static::where('draft_id', $draftId)->get()->each->persist($field, $model);
    }

    /**
     * Persist the pending attachment.
     *
     *
     * @param Editor $field
     * @param  mixed $model
     * @return void
     * @throws \Exception
     */
    public function persist(Editor $field, $model)
    {
        Attachment::create([
            'attachable_type' => get_class($model),
            'attachable_id' => $model->getKey(),
            'attachment' => $this->attachment,
            'disk' => $field->disk,
            'url' => Storage::disk($field->disk)->url($this->attachment),
        ]);

        $this->delete();
    }

    /**
     * Purge the attachment.
     *
     * @return void
     * @throws \Exception
     */
    public function purge()
    {
        Storage::disk($this->disk)->delete($this->attachment);

        $this->delete();
    }
}