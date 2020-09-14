<form method="POST" action="{{route('admin.store')}}">
    @csrf
    <input type="text" name="name">
    <input type="submit">
</form>