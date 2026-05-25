@extends('layouts.app')

@section('title', 'تعديل البيانات — أبناء الفريد')

@section('content')
<div class="page-header" style="background:linear-gradient(135deg,#1B3B8C 0%,#152d6e 100%);padding:32px 0;color:#fff;">
  <div class="container">
    <h1 style="font-size:1.6rem;font-weight:700;margin:0;">✏️ تعديل البيانات</h1>
  </div>
</div>

<div class="container" style="padding:40px 0;">
  <div class="account-layout">
    @include('account.partials.sidebar')
    <main class="account-main">
      <div style="background:#fff;border-radius:16px;padding:32px;box-shadow:0 4px 20px rgba(27,59,140,.08);">
        <h3 style="color:#1B3B8C;margin:0 0 24px;">البيانات الشخصية</h3>
        <form action="{{ route('account.profile.update') }}" method="POST">
          @csrf
          @method('PUT')
          <div class="form-grid">
            <div class="form-group">
              <label>الاسم الكامل</label>
              <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required/>
              @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>البريد الإلكتروني</label>
              <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required/>
              @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <x-phone-input name="phone" :value="old('phone', auth()->user()->phone ?? '')" label="رقم الهاتف" :required="true"/>
              @error('phone')<span class="form-error">{{ $message }}</span>@enderror
            </div>
          </div>

          <div style="border-top:1px solid #E5E7EB;margin:28px 0;padding-top:28px;">
            <h4 style="color:#1B3B8C;margin:0 0 20px;">تغيير كلمة المرور <small style="color:#9CA3AF;font-weight:400;">(اتركها فارغة إذا لم تريد التغيير)</small></h4>
            <div class="form-grid">
              <div class="form-group">
                <label>كلمة المرور الحالية</label>
                <input type="password" name="current_password" placeholder="••••••••"/>
              </div>
              <div class="form-group">
                <label>كلمة المرور الجديدة</label>
                <input type="password" name="password" placeholder="••••••••"/>
              </div>
              <div class="form-group">
                <label>تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" placeholder="••••••••"/>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-blue" style="padding:13px 32px;">حفظ التغييرات</button>
        </form>
      </div>
    </main>
  </div>
</div>
@endsection
