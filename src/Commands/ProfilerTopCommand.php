<?php

namespace Tarampampam\LaravelDatabaseQueriesProfiler\Commands;

use Illuminate\Support\Str;
use Tarampampam\LaravelDatabaseQueriesProfiler\Queries\DatabaseQuery;

/**
 * Class ProfilerTopCommand.
 *
 * @todo: Write class description.
 */
class ProfilerTopCommand extends AbstractCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'profiler:top';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show <comment>top</comment> (most "expensive") database queries';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        /** @var DatabaseQuery[] $top_stack */
        $top_stack = (array) $this->profiler->top()->toArray();

        if (! empty($top_stack)) {
            // Format table headers
            $headers = array_map(function (&$item) {
                return Str::ucfirst(str_replace(['_', '-'], ' ', strval($item)));
            }, array_keys($top_stack[0]->toArray()));

            // Format table lines
            $top_stack = array_map(function (DatabaseQuery &$query) {
                return array_map(function (&$item) {
                    return is_string($item)
                        ? chunk_split($item, 40, "\n")
                        : $item;
                }, $query->toPrintableArray());
            }, $top_stack);

            $this->table($headers, $top_stack);
        } else {
            $this->comment('There is no profiled queries');
        }
    }
}
