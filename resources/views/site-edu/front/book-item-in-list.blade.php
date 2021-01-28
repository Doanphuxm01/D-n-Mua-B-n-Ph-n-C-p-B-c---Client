@if(isset($with_image) && $with_image)
    <article class="item-book have-image col-12 pl-0 pr-0 ">
        <?php
        $_image = \App\Http\Models\Media::buildImageLink($value->avatar, '/images/no-image.jpg');
        ?>
        <div class="post-thumbnail" data-html="true"
             data-toggle="popover"
             data-trigger="hover"
             data-content="<div><b>{{$value->name}}</b><div><img src='{{$_image}}'/></div></div>">
            <a
                    href="{{\App\Http\Models\Book::buildLinkDetail($value)}}">
                <img src="{{$_image}}"
                     class="attachment-post-thumbnail size-post-thumbnail"
                     alt="{{$value->name}}">
            </a>
        </div>
        <div class="post-info">
            <h2 class="entry-title"><a
                        href="{{\App\Http\Models\Book::buildLinkDetail($value)}}"
                        title="{{$value->name}}">{{$value->name}}</a></h2>
            <div class="entry-meta">
                <p>{{$value->brief}}</p>
            </div>
        </div>
    </article>
@else
    <article class="item-book have-image col-12 pl-0 pr-0 ">
        <?php
        $_image = \App\Http\Models\Media::buildImageLink($value->avatar, '/images/no-image.jpg');
        ?>

        <div class="post-info">
            <h2 class="entry-title"><a
                        href="{{\App\Http\Models\Book::buildLinkDetail($value)}}"
                        title="{{$value->name}}">{{$value->name}}</a></h2>
            <div class="entry-meta">
                <p>{{$value->brief}}</p>
            </div>
        </div>
        <div class="post-thumbnail"
             @if($value->avatar)
             data-html="true"
             data-toggle="popover"
             data-trigger="hover"
             data-content="<div><b>{{$value->name}}</b><div><img src='{{$_image}}'/></div></div>"
                @endif
        >
            <a
                    href="{{\App\Http\Models\Book::buildLinkDetail($value)}}">
                <img src="{{$_image}}"
                     class="attachment-post-thumbnail size-post-thumbnail"
                     alt="{{$value->name}}">
            </a>
        </div>
    </article>

@endif