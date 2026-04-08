{{--
    @phpdoc
    Variabel:
    - $user: object{name,email,avatar_url,bio,phone,timezone,locale}
--}}
@extends('layouts.studentapp')

@section('title', __('Edit Profile'))

@section('content')
    <a id="edit"></a>
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => __('Dashboard'), 'route' => 'student.dashboard'],
        ['label' => __('Profile'), 'route' => 'student.profile.show'],
        ['label' => __('Edit')],
    ]])
    @if(!auth()->user()->is_validated)
        <div class="mb-6 rounded-md bg-yellow-50 p-4 border border-yellow-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">{{ __('Akun Belum Terverifikasi') }}</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>{{ __('Silakan lengkapi Surat Tugas atau Surat Bukti ASN Anda dengan mengunggah file atau menautkan link Google Drive di bawah ini. Admin akan memverifikasi akun Anda dalam waktu 1x24 jam.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-student.card :title="__('Update your profile')">
        <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-student.input name="name" :label="__('Name')" :value="old('name', $user->name)" required />
                <x-student.input name="email" type="email" :label="__('Email')" :value="old('email', $user->email)" required />
                <x-student.input name="phone" :label="__('Phone')" :value="old('phone', $user->phone)" />
                <x-student.select name="timezone" :label="__('Timezone')" :value="old('timezone', $user->timezone)" :options="array_combine(timezone_identifiers_list(), timezone_identifiers_list())" :placeholder="__('Select timezone')" />
                <x-student.select name="locale" :label="__('Locale')" :value="old('locale', $user->locale ?? 'id')" :options="['en' => 'English', 'id' => 'Bahasa Indonesia']" :placeholder="__('Select locale')" />
                <div>
                    <label for="avatar" class="block text-sm font-medium">{{ __('Avatar (Optional)') }}</label>
                    <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-gray-600" />
                    @error('avatar')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Surat Tugas / Bukti ASN fields -->
                <div>
                    <label for="surat_tugas_url" class="block text-sm font-medium mt-4">{{ __('Tautan Google Drive Surat Tugas / Bukti ASN (Publik)') }}</label>
                    <input id="surat_tugas_url" name="surat_tugas_url" type="url" value="{{ old('surat_tugas_url', $user->surat_tugas_url) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="https://drive.google.com/..." />
                    @error('surat_tugas_url')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="surat_tugas_file" class="block text-sm font-medium mt-4">{{ __('Atau Unggah File Surat Tugas / Bukti ASN (Max 5 MB)') }}</label>
                    <input id="surat_tugas_file" name="surat_tugas_file" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 dark:file:bg-gray-700 file:text-gray-700 dark:file:text-gray-200 hover:file:bg-gray-200 dark:hover:file:bg-gray-600" />
                    @error('surat_tugas_file')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($user->surat_tugas_file_path)
                        <p class="text-xs text-green-600 mt-1">Anda sudah mengunggah file sebelumnya.</p>
                    @endif
                </div>
                
                <x-student.textarea name="bio" :label="__('Bio (Optional)')" :value="old('bio', $user->bio ?? '')" rows="3" />
            </div>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('student.profile.show') }}" class="px-3 py-2 rounded-md border text-sm hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Cancel') }}</a>
                <button type="submit" class="px-3 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-700">{{ __('Save changes') }}</button>
            </div>
        </form>
    </x-student.card>
@endsection


