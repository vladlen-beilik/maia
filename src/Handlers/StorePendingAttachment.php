<?php

namespace SpaceCode\Maia\Handlers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SpaceCode\Maia\Fields\Editor;
use SpaceCode\Maia\Models\PendingAttachment;

class StorePendingAttachment
{
    public const STORAGE_PATH = '/attachments';

    /**
     * The field instance.
     *
     * @var Editor
     */
    public $field;

    /**
     * Create a new invokable instance.
     *
     * @param Editor $field
     */
    public function __construct(Editor $field)
    {
        $this->field = $field;
    }

    /**
     * Attach a pending attachment to the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function __invoke(Request $request)
    {
        $filename = $this->generateFilename($request->attachment);

        $this->abortIfFileNameExists($filename);

        $attachment = PendingAttachment::create([
            'draft_id' => $request->draftId,
            'attachment' => $request->attachment->storeAs(
                self::STORAGE_PATH,
                $filename,
                $this->field->disk
            ),
            'disk' => $this->field->disk,
        ])->attachment;

        return Storage::disk($this->field->disk)->url($attachment);
    }

    /**
     * @param string $filename
     */
    protected function abortIfFileNameExists($filename): void
    {
        if (Storage::disk($this->field->disk)->exists(self::STORAGE_PATH.'/'.$filename)) {
            abort(response()->json([
                'status' => Response::HTTP_CONFLICT,
                'message' => 'A file with this name already exists on the server'
            ], Response::HTTP_CONFLICT));
        }
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string
     */
    protected function generateFilename(UploadedFile $uploadedFile)
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

        return Str::slug($originalFilename).'-'.uniqid('', false).'.'.$uploadedFile->guessExtension();
    }
}