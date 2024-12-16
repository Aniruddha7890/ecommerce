<div class="tab-pane fade show active" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
    <div class="card border">
        <div class="card-body">
            <form action="" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Layout</label>
                    <select name="layout" id="" class="form-control">
                        <option>test</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
