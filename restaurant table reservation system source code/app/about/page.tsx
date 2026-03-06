import Image from "next/image"
import Link from "next/link"
import { Navigation } from "@/components/navigation"
import { Footer } from "@/components/footer"
import { Button } from "@/components/ui/button"
import { ArrowRight } from "lucide-react"

export default function AboutPage() {
  return (
    <main className="min-h-screen">
      <Navigation />
      
      {/* Hero */}
      <section className="pt-32 pb-16 md:pt-40 md:pb-24 bg-background">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto text-center">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Our Story
            </p>
            <h1 className="font-serif text-4xl md:text-5xl lg:text-6xl text-foreground mb-6 text-balance leading-tight">
              About Eudaimonia
            </h1>
            <p className="text-muted-foreground text-lg leading-relaxed">
              A culinary destination where ancient wisdom meets modern gastronomy, creating experiences that nourish the soul.
            </p>
          </div>
        </div>
      </section>

      {/* Story Section */}
      <section className="py-16 md:py-24 bg-muted">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <div className="relative aspect-[4/5] rounded-sm overflow-hidden">
              <Image
                src="/images/about-chef.jpg"
                alt="Our Executive Chef"
                fill
                className="object-cover"
              />
            </div>
            <div className="space-y-6">
              <p className="text-primary text-sm uppercase tracking-[0.2em] font-medium">
                The Beginning
              </p>
              <h2 className="font-serif text-3xl md:text-4xl text-foreground leading-tight">
                A Vision of Excellence
              </h2>
              <div className="space-y-4 text-muted-foreground leading-relaxed">
                <p>
                  Founded in 2018, Eudaimonia was born from a simple yet profound belief: that dining should be more than sustenance—it should be a pathway to human flourishing.
                </p>
                <p>
                  Our name comes from the ancient Greek concept of eudaimonia, which Aristotle described as the highest human good—a life of virtue, purpose, and wellbeing. We channel this philosophy into every aspect of our restaurant.
                </p>
                <p>
                  Under the guidance of Executive Chef Isabella Marchetti, our kitchen transforms the finest seasonal ingredients into edible works of art. Each dish tells a story, each flavor note carefully composed to create harmony on the palate.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Values Section */}
      <section className="py-16 md:py-24 bg-background">
        <div className="container mx-auto px-4">
          <div className="max-w-3xl mx-auto text-center mb-16">
            <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
              Our Values
            </p>
            <h2 className="font-serif text-3xl md:text-4xl text-foreground mb-6">
              Principles That Guide Us
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div className="text-center p-8 bg-card rounded-sm border border-border">
              <div className="w-16 h-16 mx-auto mb-6 flex items-center justify-center bg-primary/10 rounded-full">
                <span className="font-serif text-2xl text-primary">I</span>
              </div>
              <h3 className="font-serif text-xl text-foreground mb-3">Integrity</h3>
              <p className="text-muted-foreground text-sm leading-relaxed">
                We source only the finest ingredients from ethical suppliers, maintaining transparency in every aspect of our operation.
              </p>
            </div>

            <div className="text-center p-8 bg-card rounded-sm border border-border">
              <div className="w-16 h-16 mx-auto mb-6 flex items-center justify-center bg-primary/10 rounded-full">
                <span className="font-serif text-2xl text-primary">E</span>
              </div>
              <h3 className="font-serif text-xl text-foreground mb-3">Excellence</h3>
              <p className="text-muted-foreground text-sm leading-relaxed">
                Every detail matters. From mise en place to final presentation, we pursue perfection in all that we do.
              </p>
            </div>

            <div className="text-center p-8 bg-card rounded-sm border border-border">
              <div className="w-16 h-16 mx-auto mb-6 flex items-center justify-center bg-primary/10 rounded-full">
                <span className="font-serif text-2xl text-primary">C</span>
              </div>
              <h3 className="font-serif text-xl text-foreground mb-3">Connection</h3>
              <p className="text-muted-foreground text-sm leading-relaxed">
                We create spaces where meaningful connections flourish—between guests, staff, and the culinary arts.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Team Section */}
      <section className="py-16 md:py-24 bg-muted">
        <div className="container mx-auto px-4">
          <div className="max-w-4xl mx-auto">
            <div className="text-center mb-16">
              <p className="text-primary text-sm uppercase tracking-[0.2em] mb-4 font-medium">
                Leadership
              </p>
              <h2 className="font-serif text-3xl md:text-4xl text-foreground">
                Meet Our Team
              </h2>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div className="bg-card p-8 rounded-sm border border-border">
                <h3 className="font-serif text-xl text-foreground mb-1">Isabella Marchetti</h3>
                <p className="text-primary text-sm mb-4">Executive Chef</p>
                <p className="text-muted-foreground text-sm leading-relaxed">
                  Trained in the kitchens of Milan and Lyon, Chef Marchetti brings 20 years of culinary excellence to Eudaimonia. Her philosophy centers on honoring ingredients while pushing creative boundaries.
                </p>
              </div>

              <div className="bg-card p-8 rounded-sm border border-border">
                <h3 className="font-serif text-xl text-foreground mb-1">Marcus Chen</h3>
                <p className="text-primary text-sm mb-4">General Manager</p>
                <p className="text-muted-foreground text-sm leading-relaxed">
                  With a background in luxury hospitality spanning three continents, Marcus ensures every guest experience exceeds expectations. His attention to detail is legendary.
                </p>
              </div>

              <div className="bg-card p-8 rounded-sm border border-border">
                <h3 className="font-serif text-xl text-foreground mb-1">Elena Vasquez</h3>
                <p className="text-primary text-sm mb-4">Head Sommelier</p>
                <p className="text-muted-foreground text-sm leading-relaxed">
                  A certified Master Sommelier, Elena curates our wine program with passion and precision. Her pairings elevate each dish to new heights.
                </p>
              </div>

              <div className="bg-card p-8 rounded-sm border border-border">
                <h3 className="font-serif text-xl text-foreground mb-1">David Laurent</h3>
                <p className="text-primary text-sm mb-4">Pastry Chef</p>
                <p className="text-muted-foreground text-sm leading-relaxed">
                  David's desserts are legendary—architectural masterpieces that balance artistry with indulgence. His creations provide the perfect finale to every meal.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-24 md:py-32 bg-primary text-primary-foreground">
        <div className="container mx-auto px-4 text-center">
          <h2 className="font-serif text-3xl md:text-4xl mb-6">
            Experience Eudaimonia
          </h2>
          <p className="text-primary-foreground/80 text-lg max-w-xl mx-auto mb-10">
            We invite you to join us for an unforgettable dining experience.
          </p>
          <Link href="/book">
            <Button size="lg" variant="outline" className="border-primary-foreground text-primary-foreground hover:bg-primary-foreground hover:text-primary px-8">
              Reserve Your Table
              <ArrowRight className="ml-2 h-4 w-4" />
            </Button>
          </Link>
        </div>
      </section>

      <Footer />
    </main>
  )
}
