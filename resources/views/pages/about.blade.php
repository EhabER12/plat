@extends('layouts.app')

@section('title', 'About - Laravel App')

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">About Us</h1>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Our Story</h5>
                    <p class="card-text">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor, nisl eget ultricies 
                        tincidunt, nisl nisl aliquam nisl, eget aliquam nisl nisl eget nisl. Nullam auctor, nisl eget 
                        ultricies tincidunt, nisl nisl aliquam nisl, eget aliquam nisl nisl eget nisl.
                    </p>
                    <p class="card-text">
                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, 
                        totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae 
                        dicta sunt explicabo.
                    </p>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Our Mission</h5>
                    <p class="card-text">
                        Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur 
                        magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem 
                        ipsum quia dolor sit amet, consectetur, adipisci velit.
                    </p>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Our Vision</h5>
                    <p class="card-text">
                        Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut 
                        aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit 
                        esse quam nihil molestiae consequatur.
                    </p>
                </div>
            </div>
            
            <a href="/contact" class="btn btn-primary">Contact Us</a>
        </div>
    </div>
@endsection 