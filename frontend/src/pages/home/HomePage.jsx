import { Container, Heading } from "@chakra-ui/react";
import Banner from "./Banner";
import VideoCarousel from "@/components/common/carousels/VideoCarousel";


function HomePage() {
    return (
        <div style={{ background: "white", height: "100vh" }}>
            <Banner />
            <Container mt={"100px"} mb={"100px"} spaceY={10} background={"white"}>
                <Heading color="brand.500" fontFamily="Sora" fontWeight={"bold"} fontSize={48}>
                    NEW SCENARIOS
                </Heading>
                <VideoCarousel />
            </Container>
        </div>
    );
}

export default HomePage;
