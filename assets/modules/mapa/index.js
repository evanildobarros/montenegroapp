import Feature from 'ol/Feature';
import Map from 'ol/Map';
import View from 'ol/View';
import Point from 'ol/geom/Point';
import Overlay from 'ol/Overlay';
import {Icon, Stroke, Style} from 'ol/style';
import {LineString} from 'ol/geom';
import {OSM, Vector as VectorSource} from 'ol/source';
import {Tile as TileLayer, Vector as VectorLayer} from 'ol/layer';
import {fromLonLat} from 'ol/proj';


function mapa(coordenadas, waypoints) {
    let avgLatitude = 0.0;
    let avgLongitude = 0.0;

    for (let i = 0; i < coordenadas.coordinates.length; i++) {
        avgLongitude += coordenadas.coordinates[i][0];
        avgLatitude += coordenadas.coordinates[i][1];
    }
    avgLongitude = avgLongitude / coordenadas.coordinates.length;
    avgLatitude = avgLatitude / coordenadas.coordinates.length;

    const map = new Map({
        target: 'map',
        layers: [
            new TileLayer({
                source: new OSM()
            })
        ],
        view: new View({
            center: fromLonLat([avgLongitude, avgLatitude]),
            zoom: 13
        })
    });
    const lineString = new LineString(coordenadas.coordinates);
    // transform to EPSG:3857
    lineString.transform('EPSG:4326', 'EPSG:3857');

    // create the feature
    const feature = new Feature({
        geometry: lineString,
        name: 'Line'
    });

    const lineStyle = new Style({
        stroke: new Stroke({
            color: '#33adff',
            width: 5
        })
    });

    const sourceRoute = new VectorSource({
        features: [feature]
    });
    const vectorRoute = new VectorLayer({
        source: sourceRoute,
        style: [lineStyle]
    });
    map.addLayer(vectorRoute);

    const element = document.getElementById('popup');

    const popup = new Overlay({
        element: element,
        positioning: 'bottom-center',
        stopEvent: false,
        offset: [0, -50],
    });
    map.addOverlay(popup);

    for (let i = 0; i < waypoints.length; i++) {
        const waypoint = waypoints[i];

        console.log(waypoint);
        const point = new Point(fromLonLat(waypoint.location));

        const iconFeature = new Feature({
            geometry: point,
            name: waypoint.name,
            index: waypoint.waypoint_index
        });

        const iconStyle = new Style({
            image: new Icon({
                anchor: [0.5, 46],
                anchorXUnits: 'fraction',
                anchorYUnits: 'pixels',
                src: '/img/marker.png',
            }),
        });

        iconFeature.setStyle(iconStyle);

        const vectorSource = new VectorSource({
            features: [iconFeature],
        });

        const vectorLayer = new VectorLayer({
            source: vectorSource,
        });

        map.addLayer(vectorLayer);
    }

    // display popup on click
    map.on('click', function (evt) {
        const feature = map.forEachFeatureAtPixel(evt.pixel, function (feature) {
            return feature;
        });
        if (feature) {
            const coordinates = feature.getGeometry().getCoordinates();
            popup.setPosition(coordinates);
            $(element).popover({
                placement: 'top',
                html: true,
                content: feature.get('name'),
            });
            $(element).popover('show');
        } else {
            $(element).popover('dispose');
        }
    });

    // change mouse cursor when over marker
    map.on('pointermove', function (e) {
        if (e.dragging) {
            $(element).popover('dispose');
            return;
        }
        const pixel = map.getEventPixel(e.originalEvent);
        const features = map.getFeaturesAtPixel(pixel);
        let nonLineFeatures = 0;
        for (let i = 0; i < features.length; i++) {
            const feature = features[i];

            if (feature.getProperties().geometry.constructor.name === 'Point') {
                nonLineFeatures++;
            }
        }
        document.getElementById(map.getTarget()).style.cursor = nonLineFeatures > 0 ? 'pointer' : '';
    });
}

window.mapa = mapa;
