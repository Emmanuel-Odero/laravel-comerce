<?php

namespace Webkul\Core\Repositories;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Prettus\Repository\Traits\CacheableRepository;
use Webkul\Core\Eloquent\Repository;

class ChannelRepository extends Repository
{
    use CacheableRepository;

    /**
     * Specify model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return \Webkul\Core\Contracts\Channel::class;
    }

    /**
     * Create.
     *
     * @param  array  $data
     * @return \Webkul\Core\Contracts\Channel
     */
    public function create(array $data)
    {
        Event::dispatch('core.channel.create.before');

        $model = $this->getModel();

        foreach (core()->getAllLocales() as $locale) {
            foreach ($model->translatedAttributes as $attribute) {
                if (isset($data[$attribute])) {
                    $data[$locale->code][$attribute] = $data[$attribute];
                }
            }
        }

        $channel = parent::create($data);

        $channel->locales()->sync($data['locales']);

        $channel->currencies()->sync($data['currencies']);

        $channel->inventory_sources()->sync($data['inventory_sources']);

        $this->uploadImages($data, $channel);

        $this->uploadImages($data, $channel, 'favicon');

        Event::dispatch('core.channel.create.after', $channel);

        return $channel;
    }

    /**
     * Update.
     *
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Core\Contracts\Channel
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        Event::dispatch('core.channel.update.before', $id);

        $channel = $this->find($id);

        $channel = parent::update($data, $id, $attribute);

        $channel->locales()->sync($data['locales']);

        $channel->currencies()->sync($data['currencies']);

        $channel->inventory_sources()->sync($data['inventory_sources']);

        $this->uploadImages($data, $channel);

        $this->uploadImages($data, $channel, 'favicon');

        Event::dispatch('core.channel.update.after', $channel);

        return $channel;
    }

    /**
     * Delete.
     *
     * @param  $id
     * @return int
     */
    public function delete($id)
    {
        Event::dispatch('core.channel.delete.before', $id);

        parent::delete($id);

        Event::dispatch('core.channel.delete.after', $id);
    }

    /**
     * Upload images.
     *
     * @param  array  $data
     * @param  \Webkul\Core\Contracts\Channel  $channel
     * @param  string  $type
     * @return void
     */
    public function uploadImages($data, $channel, $type = 'logo')
    {
        if (isset($data[$type])) {
            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;
                $dir = 'channel/' . $channel->id;

                if (request()->hasFile($file)) {
                    if ($channel->{$type}) {
                        Storage::delete($channel->{$type});
                    }

                    $channel->{$type} = request()->file($file)->store($dir);
                    $channel->save();
                }
            }
        } else {
            if ($channel->{$type}) {
                Storage::delete($channel->{$type});
            }

            $channel->{$type} = null;

            $channel->save();
        }
    }
}
