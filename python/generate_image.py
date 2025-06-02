import sys
import json
from diffusers import DiffusionPipeline

def generate_image(prompt, output_path):
    try:
        # Load the Stable Diffusion 2 pipeline
        pipe = DiffusionPipeline.from_pretrained("stabilityai/stable-diffusion-2")
        pipe.to("cuda")  # Use GPU if available

        # Generate the image
        image = pipe(prompt).images[0]

        # Save the image to the specified output path
        image.save(output_path)

        # Return success response
        return {"success": True, "image_path": output_path}
    except Exception as e:
        # Return error response
        return {"success": False, "error": str(e)}

if __name__ == "__main__":
    # Read input from PHP
    input_data = json.loads(sys.stdin.read())
    prompt = input_data.get("prompt", "")
    output_path = input_data.get("output_path", "generated_image.png")

    # Generate the image
    result = generate_image(prompt, output_path)

    # Return the result as JSON
    print(json.dumps(result))
