@foreach(config('menu') as $item)
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{route($item['route'])}}">{{$item['title']}}</a>
    </li>
@endforeach
