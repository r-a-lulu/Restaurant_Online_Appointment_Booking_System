import Link from "next/link"
import Image from "next/image"
import { Navigation } from "@/components/navigation"
import { Footer } from "@/components/footer"
import { Button } from "@/components/ui/button"
import { ArrowRight, Utensils, Wine, Users, Star } from "lucide-react"

const diningZones = [
  {
    name: "The Patio",
    description: "Al fresco dining under the stars with lush garden surroundings.",
    image: "/images/zone-patio.jpg",
    href: "/dining-zones/patio",
  },
  {
    name: "Main Dining Room",
    description: "Our signature space featuring elegant ambiance and impeccable service.",
    image: "/images/zone-dining.jpg",
    href: "/dining-zones/dining-room",
  },
  {
    name: "The Bar",
    description: "Intimate cocktail experience with curated spirits and small plates.",
    image: "/images/zone-bar.jpg",
    href: "/dining-zones/bar",
  },
]

const features = [
  {
    icon: Utensils,
    title: "Culinary Excellence",
    description: "Award-winning dishes crafted from the finest seasonal ingredients.",
  },
  {
    icon: Wine,
    title: "Curated Cellar",
    description: "An extensive wine collection featuring rare vintages from around the world.",
  },
  {
    icon: Users,
    title: "Personalized Service",
    description: "Attentive staff dedicated to creating your perfect dining experience.",
  },
  {
    icon: Star,
    title: "Private Events",
    description: "Exclusive spaces for celebrations, gatherings, and corporate occasions.",
  },
]

export default function HomePage() {
  return (
    <main className="min-h-screen">
      <Navigation />
      
      {/* Hero Section */}
      <section className="relative h-screen flex items-center justify-center">
        <div className="absolute inset-0">
          <Image
            src="/images/hero-restaurant.jpg"
            alt="Eudaimonia restaurant interior"
            fill
            className="object-cover"
            priority
          />
          <div className="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70" />
        </div>
        
        <div className="relative z-10 text-center px-4 max-w-4xl mx-auto">
          <p className="text-white/80 text-sm uppercase tracking-[0.3em] mb-4 font-medium">
            Fine Dining Experience
          </p>
          <h1 className="font-serif text-5xl md:text-7xl lg:text-8xl text-white font-medium mb-6 text-balance leading-tight">
            Eudaimonia
          </h1>
          <p className="text-white/90 text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
            Where culinary artistry meets timeless elegance. Discover the essence of flourishing through an unforgettable dining journey.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/book">
              <Button size="lg" className="bg-white text-black hover:bg-white/90 px-8">
                Reserve Your Table
                <ArrowRight className="ml-2 h-4 w-4" />
              </Button>
            </Link>
            <Link href="/about">
              <Button size="lg" variant="outline" className="border-white text-white hover:bg-white/10 px-8">
                Our Story
              </Button>
            </Link>
          </div>
        </div>

        {/* Scroll Indicator */}
        <div className="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2">
          <span className="text-white/60 text-xs uppercase tracking-widest">Scroll</span>
          <div className="w-px h-12 bg-gradient-to-b from-white/60 to-transparent" />
        </div>
      </section>

      {/* Philosophy Section */}
      <section className="py-24 md:py-32 bg-background">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto text-center">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Our Philosophy
            </p>
            <h2 className="font-serif text-3xl md:text-4xl lg:text-5xl text-foreground mb-8 text-balance leading-tight">
              The Art of Flourishing
            </h2>
            <p className="text-muted-foreground text-lg leading-relaxed">
              Eudaimonia, from the ancient Greek concept of human flourishing, guides every aspect of our restaurant. We believe dining should nourish not just the body, but the soul. Every dish, every moment, every detail is crafted to contribute to your wellbeing and create memories that last a lifetime.
            </p>
          </div>
        </div>
      </section>

      {/* Dining Zones Section */}
      <section className="py-24 md:py-32 bg-muted">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Spaces
            </p>
            <h2 className="font-serif text-3xl md:text-4xl lg:text-5xl text-foreground text-balance">
              Discover Our Dining Zones
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            {diningZones.map((zone) => (
              <Link
                key={zone.name}
                href={zone.href}
                className="group relative aspect-[4/5] overflow-hidden rounded-sm"
              >
                <Image
                  src={zone.image}
                  alt={zone.name}
                  fill
                  className="object-cover transition-transform duration-700 group-hover:scale-105"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                <div className="absolute bottom-0 left-0 right-0 p-6">
                  <h3 className="font-serif text-2xl text-white mb-2">{zone.name}</h3>
                  <p className="text-white/80 text-sm">{zone.description}</p>
                  <div className="mt-4 flex items-center text-white/80 text-sm group-hover:text-white transition-colors">
                    <span>Explore</span>
                    <ArrowRight className="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-24 md:py-32 bg-background">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            {features.map((feature) => (
              <div key={feature.title} className="text-center">
                <div className="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary/10 text-primary mb-6">
                  <feature.icon className="h-5 w-5" />
                </div>
                <h3 className="font-serif text-xl text-foreground mb-3">{feature.title}</h3>
                <p className="text-muted-foreground text-sm leading-relaxed">
                  {feature.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-24 md:py-32 bg-primary text-primary-foreground">
        <div className="container mx-auto px-4 text-center">
          <h2 className="font-serif text-3xl md:text-4xl lg:text-5xl mb-6 text-balance">
            Begin Your Journey
          </h2>
          <p className="text-primary-foreground/80 text-lg max-w-2xl mx-auto mb-10">
            Reserve your table and experience the essence of fine dining at Eudaimonia. We look forward to welcoming you.
          </p>
          <Link href="/book">
            <Button size="lg" variant="outline" className="border-primary-foreground text-primary-foreground hover:bg-primary-foreground hover:text-primary px-8">
              Make a Reservation
              <ArrowRight className="ml-2 h-4 w-4" />
            </Button>
          </Link>
        </div>
      </section>

      <Footer />
    </main>
  )
}
