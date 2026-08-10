import { Box, Image } from "@chakra-ui/react";

function VideoCard({imgSrc}) {
    return (
        <Box w={"100%"} h={"100%"} >
            <Image 
                src={imgSrc} 
                w={"100%"}
                borderRadius={10}
                objectFit={"cover"}
                objectPosition={"center"}
                display={"block"}
            /> 
        </Box>
    );
}

export default VideoCard;
