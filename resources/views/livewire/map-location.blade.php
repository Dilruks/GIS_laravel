<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white shadow-sm">
                   MAPBOX
                </div>
            <div class="card-body">
                <div wire:ignore id='map' style='width: 100%; height: 80vh;'></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white shadow-sm">
                Form Input
            </div>
        <div class="card-body">
            <form wire:submit.prevent="savelocation">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Longitude</label>
                            <input wire:model="long" type="text" class="form-control" class="text-muted">
                            @error('long')
                                <small class="text-danger">{{$message}}
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Latitude</label>
                            <input wire:model="lat" type="text" class="form-control" class="text-muted">
                            @error('lat')
                                <small class="text-danger">{{$message}}
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Title</label>
                    <input wire:model="title" type="text" class="form-control">
                    @error('title')
                        <small class="text-danger">{{$message}}
                    @enderror
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea wire:model="description" class="form-control"></textarea>
                    @error('description')
                        <small class="text-danger">{{$message}}
                    @enderror
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <div class="custom-file">
                            <input wire:model="image" type="file" class="custom-file-input" id="customFile">
                            <label class="custom-file-label" for="customFile">Choose File</label>
                    <br/>
                        @error('image')
                            <small class="text-danger">{{$message}}
                        @enderror
                    </div>
                        @if ($image)
                            <img src="{{$image->temporaryUrl()}}" class="img-fluid">
                        @endif
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark text-white btn-block">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', () => {
    const defaultLocation = [ 79.861244, 6.927079]

    // load mapbox token
    mapboxgl.accessToken = '{{ env("MAPBOX_KEY") }}';
    var map = new mapboxgl.Map({
        container: 'map',
        center:defaultLocation,
        zoom: 7
    });

    const loadLocations = (geoJson) => {
        geoJson.features.forEach((location) => {
            const {geometry, properties} = location
            const {iconSize, locationId, title, image, description, longitude,latitude} = properties

            let markerElement = document.createElement('div')
            markerElement.className = 'marker' + locationId
            markerElement.id = locationId
            markerElement.style.backgroundImage = 'url(https://img.icons8.com/color/48/000000/place-marker--v2.png)'
            markerElement.style.backgroundSize = 'cover'
            markerElement.style.width = '50px'
            markerElement.style.height = '50px'

            const imageStorage = '{{asset("storage/images")}}' + '/' + image

            const content = `
                <div class="table table-sm- mt-2" style="overflow-y, auto;max-height:400px,width:100%">
                    <table>
                        <tbody>
                            
                            <tr>
                                <td>longitude</td>
                                <td>${longitude}</td>
                            </tr>
                            
                          
                            <tr>
                                <td>Latitude</td>
                                <td>${latitude}</td>
                            </tr>
                            <tr>
                                <td>City</td>
                                <td>${title}</td>
                            </tr>
                            <tr>
                                <td>Image</td>
                                <td><img src="${image}" style="width:75px; height:120px;" class="img-fluid"></td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td>${description}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `

            const popUp = new mapboxgl.Popup({
                offset:25
            }).setHTML(content).setMaxWidth("400px")

            new mapboxgl.Marker(markerElement)
            .setLngLat(geometry.coordinates)
            .setPopup(popUp)
            .addTo(map)
        })
    }

    
    loadLocations({!! $geoJson !!})
    console.log({!! $geoJson !!});
    
    window.addEventListener('locationAdded', (e) => {
        loadLocations(JSON.parse(e.detail))
    })
    console.log("long");
console.log("lati");
   
    const style = "navigation-night-v1"
    map.setStyle(`mapbox://styles/mapbox/${style}`)

    map.addControl (new mapboxgl.NavigationControl())

    map.on('click', (e) => {
            var longitude = e.lngLat.lng
            var lattitude = e.lngLat.lat

            @this.long = longitude
            @this.lat = lattitude
        })
    })
</script>
@endpush
