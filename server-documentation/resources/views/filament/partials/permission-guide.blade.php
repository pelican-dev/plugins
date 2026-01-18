@php
    use Starter\ServerDocumentation\Enums\DocumentType;
@endphp

<div class="prose prose-sm dark:prose-invert max-w-none">
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
        <strong>{{ trans('server-documentation::strings.document.type') }}</strong>
        {{ trans('server-documentation::strings.permission_guide.type_controls') }}
        <strong>{{ trans('server-documentation::strings.labels.all_servers') }}</strong>
        {{ trans('server-documentation::strings.permission_guide.all_servers_controls') }}
    </p>

    <table class="min-w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700">
                <th class="text-left py-2 pr-4 font-medium">{{ trans('server-documentation::strings.document.type') }}</th>
                <th class="text-left py-2 font-medium">{{ trans('server-documentation::strings.permission_guide.who_can_see') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach(DocumentType::cases() as $type)
                <tr>
                    <td class="py-2 pr-4">
                        <span @class([
                            'inline-flex items-center px-2 py-1 text-xs font-medium rounded-md',
                            'bg-danger-50 text-danger-700 dark:bg-danger-900/50 dark:text-danger-400' => $type === DocumentType::HostAdmin,
                            'bg-warning-50 text-warning-700 dark:bg-warning-900/50 dark:text-warning-400' => $type === DocumentType::ServerAdmin,
                            'bg-info-50 text-info-700 dark:bg-info-900/50 dark:text-info-400' => $type === DocumentType::ServerMod,
                            'bg-success-50 text-success-700 dark:bg-success-900/50 dark:text-success-400' => $type === DocumentType::Player,
                        ])>
                            {{ $type->label() }}
                        </span>
                    </td>
                    <td class="py-2 text-gray-600 dark:text-gray-300">{{ $type->description() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($showExamples ?? false)
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 mb-2">
            <strong>{{ trans('server-documentation::strings.permission_guide.toggle_title') }}</strong>
        </p>
        <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1 list-disc list-inside">
            <li><strong>{{ trans('server-documentation::strings.permission_guide.toggle_on') }}</strong> → {{ trans('server-documentation::strings.permission_guide.toggle_on_desc') }}</li>
            <li><strong>{{ trans('server-documentation::strings.permission_guide.toggle_off') }}</strong> → {{ trans('server-documentation::strings.permission_guide.toggle_off_desc') }}</li>
        </ul>

        <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 mb-2"><strong>{{ trans('server-documentation::strings.permission_guide.examples_title') }}</strong></p>
        <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1 list-disc list-inside">
            <li><strong>{{ trans('server-documentation::strings.permission_guide.example_player_all') }}</strong> → {{ trans('server-documentation::strings.permission_guide.example_player_all_desc') }}</li>
            <li><strong>{{ trans('server-documentation::strings.permission_guide.example_player_specific') }}</strong> → {{ trans('server-documentation::strings.permission_guide.example_player_specific_desc') }}</li>
            <li><strong>{{ trans('server-documentation::strings.permission_guide.example_admin_all') }}</strong> → {{ trans('server-documentation::strings.permission_guide.example_admin_all_desc') }}</li>
            <li><strong>{{ trans('server-documentation::strings.permission_guide.example_mod_specific') }}</strong> → {{ trans('server-documentation::strings.permission_guide.example_mod_specific_desc') }}</li>
        </ul>
    @endif

    <p class="text-xs text-gray-400 dark:text-gray-500 mt-4">
        {{ trans('server-documentation::strings.permission_guide.hierarchy_note') }}
    </p>
</div>
