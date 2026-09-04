import { Box, Button, Container, Grid, GridItem, Heading } from "@chakra-ui/react";
import Banner from "./Banner";
import VideoCarousel from "@/components/common/carousels/VideoCarousel";
import UsersFeedbackGrid from "@/components/common/grids/UsersFeedbackGrid";
import useGetFeedback from "@/hooks/custom-hooks/useGetFeedback";

function HomePage() {
    const feedbacks = useGetFeedback();

    return (
        <div style={{ background: "white" }}>
            <Banner />
            <Container mt={"50px"} spaceY={5} background={"white"}>
                <Heading color="brand.500" fontFamily="Sora" fontWeight={"bold"} fontSize={30}>
                    OUR USERS FEEDBACK
                </Heading>
                <UsersFeedbackGrid feedbacks={feedbacks} />
            </Container>
        </div>
    );
}

export default HomePage;
