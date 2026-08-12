////////////////////////////////////////////////////////////////////////////////////////////
//intersection and geometry conversion functions//
////////////////////////////////////////////////////////////////////////////////////////////

// corrected intersection code from https://github.com/maxogden/geojson-js-utils
// (using projected coordinates, because straight lat/lons will produce incorrect results)
// originally adapted from http://www.kevlindev.com/gui/math/intersection/Intersection.js
function lineStringsIntersect(l1, l2) {
    var intersects = [];
    for (var i = 0; i <= l1.coordinates.length - 2; ++i) {
        for (var j = 0; j <= l2.coordinates.length - 2; ++j) {
            var a1Latlon = L.latLng(l1.coordinates[i][1], l1.coordinates[i][0]),
                a2Latlon = L.latLng(l1.coordinates[i + 1][1],
                    l1.coordinates[i + 1][0]),
                b1Latlon = L.latLng(l2.coordinates[j][1], l2.coordinates[j][0]),
                b2Latlon = L.latLng(l2.coordinates[j + 1][1],
                    l2.coordinates[j + 1][0]),
                a1 = L.Projection.SphericalMercator.project(a1Latlon),
                a2 = L.Projection.SphericalMercator.project(a2Latlon),
                b1 = L.Projection.SphericalMercator.project(b1Latlon),
                b2 = L.Projection.SphericalMercator.project(b2Latlon),
                ua_t = (b2.x - b1.x) * (a1.y - b1.y) - (b2.y - b1.y) * (a1.x - b1.x),
                ub_t = (a2.x - a1.x) * (a1.y - b1.y) - (a2.y - a1.y) * (a1.x - b1.x),
                u_b = (b2.y - b1.y) * (a2.x - a1.x) - (b2.x - b1.x) * (a2.y - a1.y);
            if (u_b != 0) {
                var ua = ua_t / u_b,
                    ub = ub_t / u_b;
                if (0 <= ua && ua <= 1 && 0 <= ub && ub <= 1) {
                    var pt_x = a1.x + ua * (a2.x - a1.x),
                        pt_y = a1.y + ua * (a2.y - a1.y),
                        pt_xy = {
                            'x': pt_x,
                            'y': pt_y,
                        },
                        pt_latlon = L.Projection.SphericalMercator.unproject(pt_xy);
                    intersects.push({
                        'type': 'Point',
                        'coordinates': [pt_latlon.lng, pt_latlon.lat],
                    });
                }
            }
        }
    }
    if (intersects.length == 0) intersects = false;
    return intersects;
}

//takes GeoJSON as input, creates a GeoJSON GeometryCollection of linestrings as output
function lineify(inputGeom) {
    var outputLines = {
        'type': 'GeometryCollection',
        'geometries': [],
    };
    switch (inputGeom.type) {
        case 'GeometryCollection':
            for (var i in inputGeom.geometries) {
                var geomLines = lineify(inputGeom.geometries[i]);
                if (geomLines) {
                    for (var j in geomLines.geometries) {
                        outputLines.geometries.push(geomLines.geometries[j]);
                    }
                } else {
                    outputLines = false;
                }
            }
            break;
        case 'Feature':
            var geomLines = lineify(inputGeom.geometry);
            if (geomLines) {
                for (var j in geomLines.geometries) {
                    outputLines.geometries.push(geomLines.geometries[j]);
                }
            } else {
                outputLines = false;
            }
            break;
        case 'FeatureCollection':
            for (var i in inputGeom.features) {
                var geomLines = lineify(inputGeom.features[i].geometry);
                if (geomLines) {
                    for (var j in geomLines.geometries) {
                        outputLines.geometries.push(geomLines.geometries[j]);
                    }
                } else {
                    outputLines = false;
                }
            }
            break;
        case 'LineString':
            outputLines.geometries.push(inputGeom);
            break;
        case 'MultiLineString':
        case 'Polygon':
            for (var i in inputGeom.coordinates) {
                outputLines.geometries.push({
                    'type': 'LineString',
                    'coordinates': inputGeom.coordinates[i],
                });
            }
            break;
        case 'MultiPolygon':
            for (var i in inputGeom.coordinates) {
                for (var j in inputGeom.coordinates[i]) {
                    outputLines.geometries.push({
                        'type': 'LineString',
                        'coordinates': inputGeom.coordinates[i][j],
                    });
                }
            }
            break;
        default:
            outputLines = false;
    }
    return outputLines;
}

//takes Leaflet layers as input; produces geoJSON points of their intersections as output
function crossCheck(baseJson, drawJson) {
    var baseLines = lineify(baseJson),
        drawLines = lineify(drawJson),
        crossPoints = {
            type: 'GeometryCollection',
            geometries: [],
        };
    if (baseLines && drawLines) {
        for (var i in drawLines.geometries) {
            for (var j in baseLines.geometries) {
                var crossTest = lineStringsIntersect(drawLines.geometries[i],
                    baseLines.geometries[j]);
                if (crossTest) {
                    for (var k in crossTest) {
                        crossPoints.geometries.push(crossTest[k]);
                    }
                }
            }
        }
    }
    return crossPoints;
}

var streetNamesI = [];

function streetsCrossCheck(streetLayer, drawLayer, vueComponent, resolveStreets) {
    if (!streetLayer) {
        return;
    }
    var streetJson = streetLayer.toGeoJSON(),
        drawJson = drawLayer.toGeoJSON();

    var crossPoints = {
        type: 'GeometryCollection',
        geometries: [],
    };

    streetNamesI = [];

    for (var streetI in streetJson.features) {
        var street = streetJson.features[streetI];
        var crossTest = crossCheck(street, drawJson);
        var streetName = streetJson.features[streetI].properties['Názov ulice'];

        if (checkInside(street, drawJson)) {
            if (streetNamesI.indexOf(streetJson.features[streetI].properties)) {
                streetNamesI.push(streetName);
            }
        }

        if (crossTest.geometries.length > 0) {
            if (streetNamesI.indexOf(streetJson.features[streetI].properties)) {
                streetNamesI.push(streetName);
            }
            // console.log(streetJson.features[streetI].properties);

            for (var k in crossTest.geometries) {
                crossPoints.geometries.push(crossTest.geometries[k]);
            }
        }
    }

    // checkInside(streetJson, drawJson);
    console.log('streetNamesI', streetNamesI);
    console.log(vueComponent.streetSelectize);
    console.log('vueComponent', vueComponent);
    if (resolveStreets) {
        vueComponent.streetSelectize[0].selectize.destroy();
        $('#tags').val(streetNamesI.join(','));
        vueComponent.selectizeStreets();
    }
    let streetsInInput = $('#tags').val().split(',');
    console.log('streetsInInput', streetsInInput);
    for (var i in streetNamesI) {
        if (streetsInInput.indexOf(streetNamesI[i]) > -1) {
            vueComponent.drawStreet(streetNamesI[i]);
        }
    }
    return crossPoints;
}

function checkInside(streetJson, drawJson) {
    // console.log("streetJson", streetJson);
    // console.log(drawJson);

    var inside = true;

    for (var i in streetJson.geometry.coordinates) {
        for (var j in streetJson.geometry.coordinates[i]) {
            var point = {
                type: 'Point',
                coordinates: streetJson.geometry.coordinates[i][j],
            };
            if (gju.pointInPolygon(point, drawJson.geometry) == false) {
                inside = false;
                break;
            }
        }

        if (inside == false) {
            break;
        }
    }

    return inside;
}