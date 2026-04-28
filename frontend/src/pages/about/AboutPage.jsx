import { Container } from "@chakra-ui/react";
import AboutUs from "./AboutUs";
import OurMission from "./OurMission";
import OurTeam from "./OurTeam";

function AboutPage() {
    return (
        <Container spaceY={"50px"}>
            <AboutUs />
            <OurMission />
            <OurTeam />
        </Container>
    );
}

export default AboutPage;
