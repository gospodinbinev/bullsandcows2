<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <script src="https://kit.fontawesome.com/10ad3d18e1.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <title>{{ config('app.name') }}</title>
</head>
<body>
    
    <div class="py-5 text-center container" style="padding-bottom: 0 !important;">
        
        <!-- header -->
        <div class="row">
            <div class="col-4">
                <img style="float: right;" height="65" src="{{ asset('images/bull.png') }}" alt="bull">
            </div>
            <div class="col-4">
                <h1>Bulls & Cows</h1>
            </div>
            <div class="col-4">
                <img style="float: left;" height="60" src="{{ asset('images/cow.png') }}" alt="cow">
            </div>
        </div>
    </div>
    <div class="text-center container">
        <div class="row">
            <div class="col-12">
                <span id="timing"><i class="fa fa-clock"></i> Time: <span id="clock"></span> </span>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <a class="btn btn-lg btn-outline-primary play-btn" style="margin-top: 25px; float: right; margin-right: 70px;" href="#"><i class="fa-solid fa-play"></i> Play</a>
                <a class="btn btn-lg btn-outline-primary stop-btn" style="margin-top: 25px; float: right; margin-right: 70px; display: none !important;" href="#"><i class="fa-solid fa-stop"></i> Stop</a>
            </div>
            <div class="col-6">
                <div class="form-group has-danger">
                    <div id="divOuter">
                        <div id="divInner">
                            <label for="inputLarge">Enter 4 unique digits: </label>
                            <input type="text" class="form-control form-control-lg" id="inputLarge" maxlength="4" readonly>
                            <div class="invalid-feedback digits-input">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bs-component" style="margin-top: 15px;">
            <table class="table table-hover">
                <tr>
                    <td>Round</td>
                    <td>Entered number</td>
                    <td>Bulls / Cows</td>
                </tr>
                <tbody id="rounds">
                
                </tbody>
            </table>
        </div><!-- /example -->

        <div class="card text-white bg-success mb-3" style="max-width: 20rem; margin-top: 250px;">
            <div class="card-header">TOP 10 BEST SCORES</div>
            <div class="card-body">

                @foreach ($leaderboard as $k => $result) 
                    <p class="card-text">{{$k+1}} - {{ $result->score }} points</p>
                @endforeach
            </div>
          </div>

    </div>
  
    <!-- win message modal -->
    <div id="winMsg" class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel" style="font-size: 30px;">Congratulations, you won!</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
        </div>
    </div>

    


    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery-stopwatch.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
</body>
</html>