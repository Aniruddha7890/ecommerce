@php
    $sliderSectionTwo = json_decode($sliderSectionTwo->value);
@endphp

<div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{ route('admin.product-slider-section-two') }}" method="POST">
                @csrf
                @method('PUT')
                <h5>Category 1</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="cat_one" id="" class="form-control main-category">
                                <option value="">Select</option>
                                @foreach ($categories as $category)
                                    <option {{ $category->id == $sliderSectionTwo->category ? 'selected' : '' }}
                                        value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @php
                                $subCategories = \App\Models\SubCategory::where(
                                    'category_id',
                                    $sliderSectionTwo->category,
                                )->get();
                            @endphp
                            <label>Subcategory</label>
                            <select name="sub_cat_one" id="" class="form-control sub-category">
                                <option value="">Select</option>
                                @foreach ($subCategories as $subCategory)
                                    <option {{ $subCategory->id == $sliderSectionTwo->sub_category ? 'selected' : '' }}
                                        value="{{ $subCategory->id }}">{{ $subCategory->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @php
                                $childCategories = \App\Models\ChildCategory::where(
                                    'sub_category_id',
                                    $sliderSectionTwo->sub_category,
                                )->get();
                            @endphp
                            <label>Childcategory</label>
                            <select name="child_cat_one" id="" class="form-control child-category">
                                <option value="">Select</option>
                                @foreach ($childCategories as $childCategory)
                                    <option
                                        {{ $childCategory->id == $sliderSectionTwo->child_category ? 'selected' : '' }}
                                        value="{{ $childCategory->id }}">{{ $childCategory->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('body').on('change', '.main-category', function() {
                let id = $(this).val();
                let row = $(this).closest('.row');

                // Clear both sub-category and child-category dropdowns
                row.find('.sub-category').html('<option value="">Select</option>');
                row.find('.child-category').html('<option value="">Select</option>');

                $.ajax({
                    url: '{{ route('admin.get-subcategories') }}',
                    method: 'GET',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        let selector = row.find('.sub-category');

                        selector.html('<option value="">Select</option>');

                        $.each(data, function(i, item) {
                            selector.append(
                                `<option value="${item.id}">${item.name}</option>`);
                        })
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                })
            })

            //get child categories
            $('body').on('change', '.sub-category', function() {
                let id = $(this).val();
                let row = $(this).closest('.row');
                $.ajax({
                    url: '{{ route('admin.product.get-child-categories') }}',
                    method: 'GET',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        let selector = row.find('.child-category');
                        selector.html('<option value="">Select</option>');

                        $.each(data, function(i, item) {
                            selector.append(
                                `<option value="${item.id}">${item.name}</option>`);
                        })
                    },
                    error: function(xhr, status, error) {
                        console.log(error);
                    }
                })
            })
        })
    </script>
@endpush
