import { Box, Image } from "@chakra-ui/react";
import { imgScenario } from "@/assets";

function VideoCard() {
    return (
        <Box w={"100%"} h={"100%"} bg={"red"}>
            <Image src={imgScenario} w={"100%"} h={"100%"} objectFit={"cover"} />
        </Box>
    );
}

export default VideoCard;
