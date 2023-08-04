<!DOCTYPE html>
<html lang="en">
<head>
    <title>DeleteMe</title>
    <style>
        #d-2-container {
            /* height: 100vh; /* Set the container height to the full viewport height */
            /* overflow: auto; /*Add overflow: auto to enable scrolling within the container */
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            border: 2px dashed rgba(114, 186, 94, 0.35);
            height: 4000px;
            background: rgba(114, 186, 94, 0.05);
        }

        #d-2 {
            position: relative;
            height: 3000px;
        }

        #btn-1 {
            position: sticky;
            /* bottom: 1rem; */
            top: 1rem;
            align-self: flex-end;
            /* top: 20px; /* Adjust this value as needed to control the offset from the top */
            /* z-index: 1; /* Add a z-index to ensure the button appears above other elements */
        }
    </style>
</head>
<body>
    <div id="parent">
        <div id="d-1" style="width:500px; height: 3000px; background-color:red;">
            <p>
                Lorem, ipsum dolor sit amet consectetur adipisicing elit. Impedit adipisci molestiae officia facere dolorum excepturi. Expedita, repellendus quidem necessitatibus numquam nisi quas eum beatae voluptatem sed facere. Molestiae, libero distinctio.
            </p>
        </div>
        <div id="d-2-container">
            <div id="d-2" style="background-color:green;">
                <div>
                    <button id="btn-1" style="background-color: white;">Test</button>
                    <form>
                        <div>
                            <input>1</input>
                        </div>
                        <div>
                            <input>2</input>
                        </div>
                        <div>
                            <input>1</input>
                        </div>
                        <div>
                            <input>2</input>
                        </div>
                        <div>
                            <input>1</input>
                        </div>
                        <div>
                            <input>2</input>
                        </div>
                        <div>
                            <input>1</input>
                        </div>
                        <div>
                            <input>2</input>
                        </div>
                        <div>
                            <input>1</input>
                        </div>
                        <div>
                            <input>2</input>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>