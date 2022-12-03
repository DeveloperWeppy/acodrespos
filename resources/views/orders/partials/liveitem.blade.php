
  <div v-for="item2 in item.areas">
    <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" :title="item2.name" :style="'width: 30px; height: 30px; padding: 0; border-radius: 50%;color:white;background:'+item2.colorarea">
     
    </button>

   
 </div>
<div class="row align-items-center" v-cloak>
    <div :class="item.pulse"></div>
    <div class="col ml--2">
        <small> @{{ item.last_status }}</small><br />
        <small> @{{ item.time }}</small>
        <h4 class="mb-0">
            <a href="#!">#@{{ item.id }} @{{ item.restaurant_name }}</a>
        </h4>
        <small>@{{ item.client }}</small><br />
        <small>@{{ item.price }}</small><br />
    </div>
    <div class="col-auto">
        <a class="btn btn-sm btn-primary" :href="item.link">{{ __('Details')}}</a>
    </div>
  </div>
