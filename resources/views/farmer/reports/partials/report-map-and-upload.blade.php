   <div class="mb-3">
       <div class="d-flex justify-content-between align-items-center mb-2">
           <label class="form-label fw-semibold mb-0">
               <i class="bi bi-map me-2"></i>Select Location on Map
           </label>
           <div>
               <button type="button" class="btn btn-success btn-sm me-2" id="getLocationBtn">
                   <i class="bi bi-geo-alt me-1"></i>Use Current Location
               </button>
               <button type="button" class="btn btn-outline-danger btn-sm" id="clearLocationBtn">
                   <i class="bi bi-x-circle me-1"></i>Clear
               </button>
           </div>
       </div>

       <div id="mapContainer" class="border rounded" style="height: 400px; position: relative;">
           <div id="map" style="height: 100%; width: 100%; border-radius: 8px;"></div>
           <div id="mapLoading" class="position-absolute top-50 start-50 translate-middle text-center">
               <div class="spinner-border text-success" role="status">
                   <span class="visually-hidden">Loading map...</span>
               </div>
               <p class="mt-2">Loading map...</p>
           </div>
       </div>

       <div class="form-text">
           <i class="bi bi-info-circle me-1"></i>
           Click on the map or drag the marker to select the exact location. You can also use your current
           location.
       </div>
   </div>

   <hr>

   {{-- IMAGE UPLOAD --}}
   <div class="mb-4">
       <label for="images" class="form-label fw-semibold">
           <i class="bi bi-upload me-2"></i> Upload Images
           <span class="text-muted">(You can select multiple, Max 5MB each)</span>
       </label>
       <input type="file"
           class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
           id="images" name="images[]" accept="image/*" multiple>
       <div class="form-text">Supported formats: JPG, PNG, GIF. Maximum 5 images.</div>
       @error('images')
           <div class="invalid-feedback d-block">{{ $message }}</div>
       @enderror
       @error('images.*')
           <div class="invalid-feedback d-block">{{ $message }}</div>
       @enderror

       <div id="imagePreview" class="row mt-3"></div>
   </div>
