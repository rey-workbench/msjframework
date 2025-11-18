<?php

namespace MSJFramework\LaravelGenerator\Templates;

class ViewTemplate
{
    public static function generateList(array $fields): string
    {
        $listFields = array_slice($fields, 0, min(6, count($fields)));
        
        $tableHeaders = '';
        $tableRows = '';
        foreach ($listFields as $field) {
            $tableHeaders .= "                            <th>{$field['label']}</th>\n";
            $tableRows .= "                            <td>{{ \$item->{$field['field']} }}</td>\n";
        }
        
        return <<<BLADE
@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => \$title_menu])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0">{{ \$title_menu }}</h6>
                    @if(\$authorize->add == '1')
                        <a href="/{{ \$url_menu }}/add" class="btn btn-primary btn-sm ms-auto">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
{$tableHeaders}                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(\$items as \$item)
                                <tr>
{$tableRows}                                    <td>
                                        <a href="/{{ \$url_menu }}/show/{{ encrypt(\$item->id) }}" 
                                           class="btn btn-link btn-sm" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(\$authorize->edit == '1')
                                            <a href="/{{ \$url_menu }}/edit/{{ encrypt(\$item->id) }}" 
                                               class="btn btn-link btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(\$authorize->delete == '1')
                                            <form action="/{{ \$url_menu }}/destroy/{{ encrypt(\$item->id) }}" 
                                                  method="POST" style="display:inline;"
                                                  onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-sm text-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100" class="text-center">No data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-3">
                    {{ \$items->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

BLADE;
    }

    public static function generateAdd(array $fields): string
    {
        $formFields = '';
        foreach ($fields as $field) {
            $formFields .= self::generateFormField($field, 'add');
        }
        
        return <<<BLADE
@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => \$title_menu])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Add {{ \$title_menu }}</h6>
            </div>
            <div class="card-body">
                <form action="/{{ \$url_menu }}" method="POST">
                    @csrf
                    <div class="row">
{$formFields}                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save
                            </button>
                            <a href="/{{ \$url_menu }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

BLADE;
    }

    public static function generateEdit(array $fields): string
    {
        $formFields = '';
        foreach ($fields as $field) {
            $formFields .= self::generateFormField($field, 'edit');
        }
        
        return <<<BLADE
@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => \$title_menu])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <h6>Edit {{ \$title_menu }}</h6>
            </div>
            <div class="card-body">
                <form action="/{{ \$url_menu }}/update/{{ encrypt(\$item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
{$formFields}                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="/{{ \$url_menu }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

BLADE;
    }

    public static function generateShow(array $fields): string
    {
        $detailFields = '';
        foreach ($fields as $field) {
            $detailFields .= <<<BLADE
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">{$field['label']}</label>
                            <p class="form-control-static">{{ \$item->{$field['field']} ?? '-' }}</p>
                        </div>

BLADE;
        }
        
        return <<<BLADE
@extends('layouts.app')

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => \$title_menu])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex align-items-center">
                    <h6>View {{ \$title_menu }}</h6>
                    <div class="ms-auto">
                        @if(\$authorize->edit == '1')
                            <a href="/{{ \$url_menu }}/edit/{{ encrypt(\$item->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        @endif
                        <a href="/{{ \$url_menu }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
{$detailFields}                </div>
            </div>
        </div>
    </div>
@endsection

BLADE;
    }

    protected static function generateFormField(array $field, string $mode): string
    {
        $name = $field['field'];
        $label = $field['label'];
        $type = $field['type'];
        $required = $field['required'] === '1' ? 'required' : '';
        $value = $mode === 'edit' ? "{{ old('{$name}', \$item->{$name}) }}" : "{{ old('{$name}') }}";
        
        $colClass = $field['position'] === 'F' ? 'col-md-12' : 'col-md-6';
        
        $inputHtml = match($type) {
            'text' => "<textarea name=\"{$name}\" class=\"form-control\" rows=\"4\" {$required}>{$value}</textarea>",
            'date' => "<input type=\"date\" name=\"{$name}\" class=\"form-control\" value=\"{$value}\" {$required}>",
            'email' => "<input type=\"email\" name=\"{$name}\" class=\"form-control\" value=\"{$value}\" {$required}>",
            'number', 'currency' => "<input type=\"number\" name=\"{$name}\" class=\"form-control\" value=\"{$value}\" step=\"0.01\" {$required}>",
            default => "<input type=\"text\" name=\"{$name}\" class=\"form-control\" value=\"{$value}\" {$required}>",
        };
        
        return <<<BLADE
                        <div class="{$colClass} mb-3">
                            <label for="{$name}" class="form-label">{$label}</label>
                            {$inputHtml}
                            @error('{$name}')
                                <div class="text-danger text-xs mt-1">{{ \$message }}</div>
                            @enderror
                        </div>

BLADE;
    }
}
