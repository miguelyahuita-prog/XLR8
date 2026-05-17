"use client";

import Link from "next/link";
import { money } from "@/lib/format";

type Product = {
  id: number;
  name: string;
  slug: string;
  description: string;
  price: string;
  stock: number;
  imageUrl: string | null;
  isCustomizable: boolean;
  category?: { name: string } | null;
};

export function ProductCard({ product }: { product: Product }) {
  function addToCart() {
    const current = JSON.parse(localStorage.getItem("flores_alesli_cart") ?? "[]");
    const existing = current.find((item: { id: number }) => item.id === product.id);
    const next = existing
      ? current.map((item: { id: number; quantity: number }) => item.id === product.id ? { ...item, quantity: item.quantity + 1 } : item)
      : [...current, { id: product.id, name: product.name, price: product.price, imageUrl: product.imageUrl, quantity: 1 }];
    localStorage.setItem("flores_alesli_cart", JSON.stringify(next));
    window.dispatchEvent(new Event("cart:updated"));
  }

  return (
    <article className="card">
      {product.imageUrl ? <img className="product-image" src={product.imageUrl} alt={product.name} /> : null}
      <div className="card-body">
        <span className="pill">{product.category?.name ?? "Flores"}</span>
        <h3>{product.name}</h3>
        <p className="muted">{product.description}</p>
        <p className="price">{money(product.price)}</p>
        <div style={{ display: "flex", gap: 10, flexWrap: "wrap" }}>
          <button className="button" onClick={addToCart} disabled={product.stock <= 0}>Agregar</button>
          <Link className="button secondary" href={`/catalogo/${product.slug}`}>Detalle</Link>
        </div>
      </div>
    </article>
  );
}
