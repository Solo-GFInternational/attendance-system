{{--
    Role picker for the Add / Edit user forms.
    Pass $selected as 'user' or 'admin'.
--}}

@php $role = old('role', $selected ?? 'user'); @endphp

<style>
    .role-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 12px;
    }

    .role-option {
        position: relative;
        display: block;
        border: 2px solid #dee2e6;
        border-radius: 12px;
        padding: 16px 16px 16px 48px;
        cursor: pointer;
        background: #ffffff;
        transition: border-color .15s ease, background .15s ease;
        margin: 0;
    }

    .role-option:hover {
        border-color: #9ec5fe;
    }

    .role-option input {
        position: absolute;
        left: 16px;
        top: 19px;
        margin: 0;
    }

    .role-option:has(input:checked) {
        border-color: #0d6efd;
        background: #f3f8ff;
    }

    .role-option-title {
        font-weight: 700;
        color: #212529;
        margin-bottom: 6px;
    }

    .role-option ul {
        margin: 0;
        padding-left: 18px;
        color: #6c757d;
        font-size: 13px;
    }
</style>

<div class="mb-4">

    <div class="form-label">Role</div>

    <div class="role-options">

        <label class="role-option">
            <input type="radio" name="role" value="user" class="form-check-input"
                   {{ $role === 'user' ? 'checked' : '' }} required>

            <div class="role-option-title">👤 User</div>

            <ul>
                <li>Add and edit employees</li>
                <li>View attendance and pull reports</li>
                <li>Cannot create user or admin accounts</li>
            </ul>
        </label>

        <label class="role-option">
            <input type="radio" name="role" value="admin" class="form-check-input"
                   {{ $role === 'admin' ? 'checked' : '' }}>

            <div class="role-option-title">👑 Admin</div>

            <ul>
                <li>Everything a user can do</li>
                <li>Create, edit and delete user and admin accounts</li>
            </ul>
        </label>

    </div>

    @error('role')
        <div class="text-danger small mt-2">{{ $message }}</div>
    @enderror

</div>
